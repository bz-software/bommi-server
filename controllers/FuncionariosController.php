<?php

namespace app\controllers;

use Yii;
use yii\base\Controller;
use app\models\System;
use app\models\Funcionarios;
use app\helpers\ControllerHelper;
use app\models\UrlCadastroFuncionarios;

class FuncionariosController extends Controller{

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

    public function actionCriarUrlCadastro(){
        define('LIMITE_CADASTROS', 10); 
        $requisicao = Yii::$app->request->post('dados');
        if($requisicao['countCadastros'] > 10 || $requisicao['countCadastros'] < 1 ){
            return $this->sendJson([
                'error' => [
                    'countCadastros' => 'Digite um valor entre 1 e 10'
                ]
            ]);
        }

        if(intval(SistemaController::urlCadastroDisponivel($requisicao['sistema'])) > 0){
            throw new \yii\web\HttpException(401);
        }

        $expires = (string) strtotime(date('Y-m-d H:i:s', strtotime("+7 days")));
        $token = \Yii::$app->security->generateRandomString();
        for($i = 1; $i <= $requisicao['countCadastros']; $i++){
            $urlCadastroFuncionarios = new UrlCadastroFuncionarios();
            $urlCadastroFuncionarios->ucf_token = $token;
            $urlCadastroFuncionarios->ucf_expire = $expires;
            $urlCadastroFuncionarios->ucf_system = intval($requisicao['sistema']);
            // $urlCadastroFuncionarios->save();

            if(!$urlCadastroFuncionarios->validate()){
                return $this->sendJson([
                    'error'=>$urlCadastroFuncionarios->getFirstErrors()
                ]);
            }
        }
        
        
    }
    
    
}