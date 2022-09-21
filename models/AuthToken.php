<?php

namespace app\models;

use app\helpers\resources\FuncionariosResource;
use Yii;

/**
 * This is the model class for table "auth_token".
 *
 * @property int $aut_id
 * @property int $aut_user_id
 * @property string $aut_token
 * @property string $aut_expire
 * @property string $aut_address
 */
class AuthToken extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'auth_token';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['aut_user_id', 'aut_token', 'aut_expire', 'aut_address'], 'required'],
            [['aut_user_id'], 'integer'],
            [['aut_expire'], 'string', 'max' => 50],
            [['aut_token'], 'string', 'max' => 250],
            [['aut_address'], 'string', 'max' => 150],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'aut_id' => 'Aut ID',
            'aut_user_id' => 'Aut User ID',
            'aut_token' => 'Aut Token',
            'aut_expire' => 'Aut Expire',
            'aut_address' => 'Aut Address',
        ];
    }

    public static function setAccessToken($userId){
        $auth = self::find()->where(['aut_user_id' => $userId])
                ->andWhere(['>', 'aut_expire', strtotime(date('Y-m-d H:i:s'))])
                ->andWhere(['aut_address' => Yii::$app->getRequest()->getUserIP()])->one();
        
        if(empty($auth)){
            $auth = new AuthToken();
            $auth->aut_token = \Yii::$app->security->generateRandomString();
            $auth->aut_user_id = $userId;
            $auth->aut_address = Yii::$app->getRequest()->getUserIP();
            $auth->aut_expire = (string) strtotime(date('Y-m-d H:i:s', strtotime("+3 hours")));
            $auth->save();
        }else{
            $auth->aut_token = \Yii::$app->security->generateRandomString();
            $auth->aut_address = Yii::$app->getRequest()->getUserIP();
            $auth->aut_expire = (string) strtotime(date('Y-m-d H:i:s', strtotime("+3 hours")));
            $auth->save();
        }

        return $auth;
    }

    public static function validateToken($token){
        $auth = self::find()->where(['aut_token' => $token])
                ->andWhere(['>', 'aut_expire', strtotime(date('Y-m-d H:i:s'))])
                ->andWhere(['aut_address' => Yii::$app->getRequest()->getUserIP()])
                ->one();
        
        if(!empty($auth)){
            return true;
        }else{
            return false;
        }
    }

    public static function findUserByAccessToken($token, $onlyPublicData = false){
        $auth = self::find()->where(['aut_token' => $token])->one();

        // if($onlyPublicData){

        // }

        return FuncionariosResource::findIdentity($auth->aut_user_id);
    }

    /**
     * {@inheritdoc}
     * @return queries\AuthTokenQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new queries\AuthTokenQuery(get_called_class());
    }
}
