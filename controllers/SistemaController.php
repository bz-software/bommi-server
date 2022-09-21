<?php

namespace app\controllers;

use Yii;
use yii\base\Controller;
use app\models\System;
use app\models\Funcionarios;
use app\helpers\ControllerHelper;
use app\models\AuthToken;
use app\models\Servicos;
use app\helpers\Formatter;
use app\models\Avatar;
use app\models\AvatarUpload;
use app\models\Cover;
use app\models\CoverUpload;
use yii\web\UploadedFile;
use app\models\UrlCadastroFuncionarios;

class SistemaController extends Controller{

    public function sendJson($data){
        return ControllerHelper::sendJson($data);
    }

    public function behaviors(){
        return [
            'corsFilter' => [
                'class' => \yii\filters\Cors::className(),
            ],
        ];
    }

    public function actionIndex(){
        $teste = System::findOne(1);
        return $this->sendJson($teste);
    }

    public function actionCadastrar(){
        $Funcionarios = new Funcionarios();
        $Sistema = new System();
        $requisicao = Yii::$app->request->post('dados');
        $access_token = Yii::$app->request->post('access_token');
        
        if(!empty($access_token) && AuthToken::validateToken($access_token)){
            $identity = AuthToken::findUserByAccessToken($access_token);
            return $this->registerSystemByAccessToken($identity, $requisicao['system']);
        }

        if(!empty($access_token) && !AuthToken::validateToken($access_token)){
            throw new \yii\web\HttpException(401);
        }

        $Funcionarios->attributes = $requisicao['funcionarios'];
        $Funcionarios->validate();
        
        $Sistema->attributes = $requisicao['system'];
        $Sistema->sys_data_inicio = date('Y-m-d');
        $Sistema->sys_excluido = 0;
        $Sistema->validate();
        
        if(!$Funcionarios->errors && !$Sistema->errors){
            $funcionarioPorEmail = Funcionarios::find()->where(['fun_email' => $Funcionarios->fun_email])->one();
            $sistemaPorDominio = System::find()->where(['sys_dominio' => $Sistema->sys_dominio])->one();
            $sistemaPorCnpj = System::find()->where(['sys_cnpj' => $Sistema->sys_cnpj])->one();

            if(!empty($sistemaPorCnpj)){
                $Sistema->addError('sys_cnpj', "Já existe uma empresa cadastrada com o CNPJ informado");
            }

            if(!empty($funcionarioPorEmail)){                
                $Funcionarios->addError('fun_email', "Já existe um usuário cadastrado com o e-mail informado");
            }

            if(!empty($sistemaPorDominio)){
                $Sistema->addError('sys_dominio', "Já existe uma empresa cadastrada com o domínio informado");
            }
            
            if(!$Funcionarios->errors && !$Sistema->errors){  
                $Funcionarios->fun_senha = password_hash($Funcionarios->fun_senha, PASSWORD_DEFAULT);
                $Funcionarios->fun_primeiro_nome = Funcionarios::getPrimeiroNome($Funcionarios->fun_nome);
                $Funcionarios->fun_avatar = "imgs/user/avatar/default.jpg";
                $Sistema->sys_capa = "imgs/system/cover/default.jpg";
                $Sistema->sys_logo = "imgs/system/avatar/default.jpg";
                $Funcionarios->save();
            
                $Sistema->sys_cliente = $Funcionarios->fun_id;
                $Sistema->save();

                $this->setServicosPadroes($Sistema->sys_id);

                return $this->sendJson( [
                    'message' => ['200'],
                    'fun_id' => $Funcionarios->fun_id
                ]);
            }
        }

        return $this->sendJson( [
            'errors' => [
                'funcionarios' => $Funcionarios->getFirstErrors(), 
                'system' => $Sistema->getFirstErrors()
            ]
        ]);

    }

    private function setServicosPadroes($idSistema){
        $servicos = Servicos::servicosPadroes($idSistema);

        foreach($servicos as $servico){
            $Servicos = new Servicos();

            $Servicos->attributes = $servico;
            $Servicos->save();
        }
    }

    public function actionBuscar(){
        $dominio = strtolower(Yii::$app->request->post('domain')); 
        $system = System::findByDominio($dominio);

        if(!empty($system)){
            $servicos = SistemaController::buscarServicosPorSistema($system['sys_id']);

            return $this->sendJson([
                'system' => [
                    'data' => $system,
                    'servicos' => Servicos::formatarParaRetorno($servicos),
                ],
            ]);
        }else{
            return  $this->sendJson(['error' => 'not-found']);
        }
    }

    public function actionBuscarPorUsuario(){
        $token = Yii::$app->request->post('token');
        if(!empty($token) && AuthToken::validateToken($token)){
            $identity = AuthToken::findUserByAccessToken($token);
            $sistemas = System::find()->select(['sys_id', 'sys_nome_empresa'])->where(['sys_cliente'=>$identity['fun_id']])->all();
            return $this->sendJson([
                'sistemas'=>$sistemas
            ]);
        }

        if(!empty($token) && !AuthToken::validateToken($token)){
            throw new \yii\web\HttpException(401);
        }

    }

    public function actionEstados(){
        $estados = System::find()->select(['sys_uf'])->groupBy(['sys_uf'])->orderBy(['sys_uf' => SORT_ASC])->all();

        return $this->sendJson(['estados' => $estados]);
    }

    public function actionCidades(){
        $estado = Yii::$app->request->post('estado');
        $cidades = System::find()->select(['sys_cidade'])->where(['sys_uf' => $estado])->groupBy(['sys_cidade'])->orderBy(['sys_cidade' => SORT_ASC])->all();

        return $this->sendJson(['cidades' => $cidades]);
    }

    public function actionBairros(){
        $cidade = Yii::$app->request->post('cidade');
        $bairros = System::find()->select(['sys_bairro'])->where(['sys_cidade' => $cidade])->groupBy(['sys_bairro'])->orderBy(['sys_bairro' => SORT_ASC])->all();

        return $this->sendJson(['bairros' => $bairros]);
    }

    public function actionSistemas(){
        $cidade = Yii::$app->request->post('cidade');
        $bairro = Yii::$app->request->post('bairro');

        $sistemas = System::find()
                    ->select(['sys_nome_empresa', 'sys_dominio', 'sys_endereco'])
                    ->where(['sys_cidade' => $cidade])
                    ->andWhere(['sys_bairro' => $bairro])
                    ->orderBy(['sys_nome_empresa' => SORT_ASC])
                    ->all();

        return $this->sendJson(['sistemas' => $sistemas]);
    }

    private function registerSystemByAccessToken($identity, $sistema){
        $Sistema = new System();

        $Sistema->attributes = $sistema;
        $Sistema->sys_data_inicio = date('Y-m-d');
        $Sistema->sys_excluido = 0;
        $Sistema->validate();

        if(!$Sistema->errors){
            $sistemaPorDominio = System::find()->where(['sys_dominio' => $Sistema->sys_dominio])->one();
            $sistemaPorCnpj = System::find()->where(['sys_cnpj' => $Sistema->sys_cnpj])->one();

            if(!empty($sistemaPorCnpj)){
                $Sistema->addError('sys_cnpj', "Já existe uma empresa cadastrada com o CNPJ informado");
            }

            if(!empty($sistemaPorDominio)){
                $Sistema->addError('sys_dominio', "Já existe uma empresa cadastrada com o domínio informado");
            }

            if(!$Sistema->errors){
                $Sistema->sys_capa = "imgs/cover/default.jpg";
                $Sistema->sys_logo = "imgs/avatar/default.jpg";
            
                $Sistema->sys_cliente = $identity->fun_id;
                $Sistema->save();

                return $this->sendJson( [
                    'message' => ['200'],
                    'fun_id' => $identity->fun_id,
                ]);
            }
        }

        return $this->sendJson( [
            'errors' => [
                'system' => $Sistema->getFirstErrors()
            ]
        ]);
    }

    public function actionUrlCadastroDisponivel(){
        $id = Yii::$app->request->post('id');

        $urlDisponiveis = UrlCadastroFuncionarios::find()
                          ->where(['ucf_system' => $id])
                          ->andWhere(['>', 'ucf_expire', strtotime(date('Y-m-d H:i:s'))])
                          ->count();

        return $this->sendJson([
            'cadastrosRestantes' => $urlDisponiveis
        ]);
    }

    public static function urlCadastroDisponivel($id){
        $urlDisponiveis = UrlCadastroFuncionarios::find()
                          ->where(['ucf_system' => $id])
                          ->andWhere(['IS', 'ucf_usuario_cadastrado', null ])
                          ->andWhere(['>', 'ucf_expire', strtotime(date('Y-m-d H:i:s'))])
                          ->count();

        return $urlDisponiveis;
    }

    public function actionCriarServico(){
        $Servicos = new Servicos();

        $servico = Yii::$app->request->post('servico');
        $token = Yii::$app->request->post('token');

        if(!empty($token) && !AuthToken::validateToken($token)){
            throw new \yii\web\HttpException(401);
        }

        $Servicos->attributes = $servico;
        $Servicos->svs_ativo = $servico['svs_ativo'] == 'true' ? 1 : 0;
        $Servicos->svs_preco = Formatter::realParaFloat($servico['svs_preco']);
        if(!$Servicos->validate()){
            return $this->sendJson([
                'error' => $Servicos->getFirstErrors()
            ]);
        }else{
            $Servicos->save();
            
            return $this->sendJson([
                'status' => 1,
                'servicos' => $this->buscarServicosPorSistema($servico['svs_system'])
            ]);
        }
    }

    public function actionBuscarServicos(){
        $idSistema = Yii::$app->request->post('idSistema');

        return $this->sendJson([
            'servicos' => Servicos::formatarParaRetorno($this->buscarServicosPorSistema($idSistema))
        ]);
    }

    public function actionBuscarServico(){
        $idServico = Yii::$app->request->post('id');

        return $this->sendJson([
            'servico' => Servicos::formatarParaRetorno(Servicos::findOne($idServico), true)
        ]);
    }

    public function actionAlterarServico(){
        $servico = Yii::$app->request->post('servico');
        $token = Yii::$app->request->post('token');

        if(!empty($token) && !AuthToken::validateToken($token)){
            throw new \yii\web\HttpException(401);
        }

        $Servicos = Servicos::findOne($servico['svs_id']);

        if(!empty($Servicos)){
            $Servicos->attributes = $servico;
            $Servicos->svs_ativo = $servico['svs_ativo'] == 'true' ? 1 : 0;
            $Servicos->svs_preco = Formatter::realParaFloat($servico['svs_preco']);

            if(!$Servicos->validate()){
                return $this->sendJson([
                    'error' => $Servicos->getFirstErrors()
                ]);
            }else{
                $Servicos->save();
                
                return $this->sendJson([
                    'servicos' => $this->buscarServicosPorSistema($servico['svs_system'])
                ]);
            }
        }
    }

    public function actionDeletarServico(){
        $id = Yii::$app->request->post('id');
        $token = Yii::$app->request->post('token');

        if(!empty($token) && !AuthToken::validateToken($token)){
            throw new \yii\web\HttpException(401);
        }

        $Servicos = Servicos::findOne($id);
        $Servicos->delete();

        throw new \yii\web\HttpException(200);
    }

    public static function buscarServicosPorSistema($id){
        return Servicos::find()
                       ->where(['svs_system' => $id])
                       ->andWhere(['=', 'svs_excluido', 0])
                       ->orderBy(['svs_id' => SORT_DESC])
                       ->all();
    }

    public function actionUploadAvatar(){
        $token = Yii::$app->request->post('token');
        $image = $_FILES['file'];
        $idSistema = Yii::$app->request->post('idSistema');

        $AvatarUpload = new AvatarUpload();
        $avatar = new Avatar();

        if(!empty($token) && !AuthToken::validateToken($token)){
            throw new \yii\web\HttpException(401);
        }

        $AvatarUpload->imageFile = UploadedFile::getInstanceByName('file');
        $path = $AvatarUpload->upload($idSistema);
        if($path){
            Avatar::setTodosNaoAtual($idSistema);

            $avatar->avt_caminho = $path;
            $avatar->avt_atual = 1;
            $avatar->avt_data = (string) strtotime(date('d-m-Y H:i:s'));
            $avatar->avt_sys_id = $idSistema;

            if($avatar->save()){
                return $this->sendJson([
                    'file' => ControllerHelper::pathToSystemAvatar() . $avatar->avt_caminho
                ]);
            }else{
                return $this->sendJson([
                    'error' => $avatar->getErrors(),
                ]);
            }
        }else{
            $errors = $AvatarUpload->getFirstErrors();
            return $this->sendJson([
                'error' => reset($errors),
            ]);
        }
    }

    public function actionUploadCapa(){
        $token = Yii::$app->request->post('token');
        $idSistema = Yii::$app->request->post('idSistema');

        $CoverUpload = new CoverUpload();
        $capa = new Cover();

        if(!empty($token) && !AuthToken::validateToken($token)){
            throw new \yii\web\HttpException(401);
        }

        $CoverUpload->imageFile = UploadedFile::getInstanceByName('file');
        $path = $CoverUpload->upload($idSistema);
        if($path){
            Cover::setTodosNaoAtual($idSistema);

            $capa->cov_caminho = $path;
            $capa->cov_atual = 1;
            $capa->cov_data = (string) strtotime(date('d-m-Y H:i:s'));
            $capa->cov_sys_id = $idSistema;

            if($capa->save()){
                return $this->sendJson([
                    'file' => ControllerHelper::pathToSystemCover() . $capa->cov_caminho
                ]);
            }else{
                return $this->sendJson([
                    'error' => $capa->getErrors(),
                ]);
            }
        }else{
            $errors = $CoverUpload->getFirstErrors();
            return $this->sendJson([
                'error' => reset($errors),
            ]);
        }
    }

    public function actionChange(){
        $idSistema = Yii::$app->request->post('idSistema');
        $idUser = Yii::$app->request->post('idUser');

        System::setPrincipal($idSistema, $idUser);

        $system = System::findByFuncionarioId($idUser);
        $systems = System::findAllNaoAtivos($system['sys_id'], $idUser);

        return $this->sendJson([
            'system' => [
                'data' => $system,
            ],
            'systems' => $systems,
        ]);
    }
}