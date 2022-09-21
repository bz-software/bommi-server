<?php

namespace app\controllers;

use Yii;
use yii\base\Controller;
use app\models\Clientes;
use app\helpers\ControllerHelper;
use app\helpers\resources\ClientesResource;
use app\models\AuthToken;
use app\models\System;

class UserController extends Controller{

    public function sendJson($data){
        return ControllerHelper::sendJson($data);
    }

    public function behaviors(){
        return [
            'corsFilter' => [
                'class' => \yii\filters\Cors::className(),
            ]
        ];
    }

    public function actionLogin(){
        $request = Yii::$app->request->post('data');
        $error = $this->validateLogin($request);

        if(is_array($error)){
            return $this->sendJson(['error' => $error]);
        }

        if(!empty($request['access_token']) && AuthToken::validateToken($request['access_token'])){
            $identity = AuthToken::findUserByAccessToken($request['access_token']);
            $auth = AuthToken::setAccessToken($identity->cli_id);

            return $this->sendJson([
                'access_token' => $auth->aut_token,
                'type' => 'renovate'
            ]);
        }else{
            $identity = Clientes::findOne(['cli_email' => $request['cli_email']]);
            if(!empty($identity)){

                if($identity->validatePassword($request['cli_senha'])){

                    Yii::$app->user->login($identity);
                    $auth = AuthToken::setAccessToken($identity->cli_id);

                    return $this->sendJson([
                        'access_token' => $auth->aut_token,
                        'type' => 'create'
                    ]);
                }else{
                    $this->sendMessage(null, "Senha inválida");
                }

            }else{
                $this->sendMessage("E-mail não encontrado", null);
            }
        }
        
    }

    public function actionLoginCadastro(){
        $request = Yii::$app->request->post('data');
        $error = $this->validateLogin($request);

        if(is_array($error)){
            return $this->sendJson(['error' => $error]);
        }

        $identity = Clientes::findOne(['cli_email' => $request['cli_email']]);
        if(!empty($identity)){

            if($identity->validatePassword($request['cli_senha'])){

                Yii::$app->user->login($identity);
                $auth = AuthToken::setAccessToken($identity->cli_id);

                return $this->sendJson([
                    'access_token' => $auth->aut_token,
                    'user_data' => ClientesResource::findOne($identity->cli_id)
                ]);
            }else{
                $this->sendMessage(null, "Senha inválida");
            }

        }else{
            $this->sendMessage("E-mail não encontrado", null);
        }
    }

    public function actionAuth(){
        $access_token = Yii::$app->request->post('access_token');

        if(!empty($access_token) && AuthToken::validateToken($access_token)){
            throw new \yii\web\HttpException(200);
        }else{
            throw new \yii\web\HttpException(401);
        }
    }

    public function actionData(){
        $access_token = Yii::$app->request->post('access_token');

        if(!empty($access_token) && AuthToken::validateToken($access_token)){
            $identity = AuthToken::findUserByAccessToken($access_token, true);
            $system = System::findByClienteId($identity->cli_id);

            return $this->sendJson([
                'user_data' => $identity,
                'system' => $system,
                'access_token' => $access_token
            ]);
        }else{
            throw new \yii\web\HttpException(401);
        }
    }

    public function actionIsLogged(){
        $token = Yii::$app->request->post('access_token');

        if(!empty($token) && AuthToken::validateToken($token)){
            $identity = AuthToken::findUserByAccessToken($token, true);
            $system = System::findByFuncionarioId($identity->fun_id);
            $systems = System::findAllNaoAtivos($system['sys_id'], $identity['fun_id']);

            $servicos = SistemaController::buscarServicosPorSistema($system['sys_id']);

            return $this->sendJson([
                'user_data' => $identity,
                'system' => [
                    'data' => $system,
                    'servicos' => Servicos::formatarParaRetorno($servicos),
                ],
                'systems' => $systems,
                'access_token' => $token
            ]);
        }else{
            throw new \yii\web\HttpException(401);
        }
    }

    public function actionLogout(){
        $access_token = Yii::$app->request->post('access_token');

        $identity = AuthToken::findUserByAccessToken($access_token);
        AuthToken::setAccessToken($identity->cli_id);

        throw new \yii\web\HttpException(200);
    }

    private function validateLogin($data){
        $message = "Campo obrigatório";
        $messages = array();

        if(!isset($data['cli_email']) || empty($data['cli_email'])){
            $messages['cli_email'] = $message;
        }

        if(!isset($data['cli_senha']) || empty($data['cli_senha'])){
            $messages['cli_senha'] = $message;
        }
        
        if(!empty($messages)){
            return $messages;
        }else{
            return true;
        }
    }

    private function sendMessage($email=null, $senha=null){
        $messages = array();

        if($email){
            $messages['cli_email'] = $email;
        }

        if($senha){
            $messages['cli_senha'] = $senha;
        }

        return $this->sendJson(['error' => $messages]);
    }
}