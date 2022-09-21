<?php

namespace app\helpers;

use Yii;

class ControllerHelper {
    public static function sendJson($data){
        $response = Yii::$app->response;
        $response->format = \yii\web\Response::FORMAT_JSON;
        $response->data = $data;
        return $response;
    }

    public static function pathToSystemAvatar($saving = false){
        if($saving)
            return '../web/imgs/system/avatar/';
        else
            return 'imgs/system/avatar/';
    }

    public static function pathToSystemCover($saving = false){
        if($saving)
            return '../web/imgs/system/cover/';
        else
            return 'imgs/system/cover/';
    }
}

