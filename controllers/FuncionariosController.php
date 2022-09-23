<?php

namespace app\controllers;

use Yii;
use yii\base\Controller;
use app\models\System;
use app\models\Funcionarios;
use app\helpers\ControllerHelper;
use app\models\UrlCadastroFuncionarios;
use app\models\AuthToken;
use app\models\Cargos;

class FuncionariosController extends Controller{
    protected $limiteCadastro = 5;

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

    public function actionCreate(){
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        $cadastroFuncionario = Yii::$app->request->post('cadastroFuncionario');


        if(intval(UrlCadastroFuncionarios::totalDisponivel($cadastroFuncionario['ucf_system'])) > $this->limiteCadastro){
            $error['messages']['ucf_token'] = "Limite de 5 códigos ativos alcançado";
            throw new \yii\web\HttpException(400, json_encode($error));
        }

        $expires = (string) strtotime(date('Y-m-d H:i:s', strtotime("+7 days")));
        $token = \Yii::$app->security->generateRandomString();
       
        $urlCadastroFuncionarios = new UrlCadastroFuncionarios();
        $urlCadastroFuncionarios->ucf_token = $token;
        $urlCadastroFuncionarios->ucf_expire = $expires;
        $urlCadastroFuncionarios->ucf_system = intval($cadastroFuncionario['ucf_system']);
        $urlCadastroFuncionarios->ufc_car_id = $cadastroFuncionario['ufc_car_id'];
        

        if(!$urlCadastroFuncionarios->validate()){
            Yii::$app->response->statusCode = 400;
            
            $error['messages'] = $urlCadastroFuncionarios->getFirstErrors();
            return $error['messages'];
        }else{
            $urlCadastroFuncionarios->save();

            return[
                'codigoCadastro'  => $urlCadastroFuncionarios->attributes,
                'codigosCadastro' => UrlCadastroFuncionarios::disponiveis($cadastroFuncionario['ucf_system'])
            ];
        }
    }

    public function actionIndex(){
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        if(!empty($access_token) && !AuthToken::validateToken($access_token)){
            throw new \yii\web\HttpException(401);
        }

        $idSistema = Yii::$app->request->post('idSistema');

        if(!empty($idSistema)){
            return [
                'codigosCadastro' => UrlCadastroFuncionarios::disponiveis($idSistema),
                'cargos' => Cargos::porSistema($idSistema)
            ];
        }
    }
    
    
}