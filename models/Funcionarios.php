<?php

namespace app\models;

use Yii;
use yii\web\IdentityInterface;

/**
 * This is the model class for table "Funcionarios".
 *
 * @property int $fun_id
 * @property string $fun_nome
 * @property string $fun_primeiro_nome
 * @property string $fun_telefone
 * @property string $fun_email
 * @property string|null $fun_avatar
 * @property int $fun_excluido
 * @property string $fun_data_criacao
 * @property string $fun_data_altera
 * @property string $fun_senha
 *
 * @property System[] $systems
 */
class Funcionarios extends \yii\db\ActiveRecord implements IdentityInterface
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'funcionarios';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['fun_nome', 'fun_senha', 'fun_email'], 'required', 'message' => 'Campo obrigatório'],
            [['fun_excluido'], 'integer'],
            [['fun_data_criacao', 'fun_data_altera'], 'safe'],
            [['fun_nome', 'fun_telefone', 'fun_email', 'fun_avatar', 'fun_primeiro_nome',], 'string', 'max' => 150],
            [['fun_senha'],'string', 'max' => 250],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'fun_id' => 'Cli ID',
            'fun_nome' => 'Cli Nome',
            'fun_primeiro_nome' => 'Primeiro nome',
            'fun_telefone' => 'Cli Telefone',
            'fun_email' => 'Cli Email',
            'fun_avatar' => 'Cli Avatar',
            'fun_excluido' => 'Cli Excluido',
            'fun_data_criacao' => 'Cli Data Criacao',
            'fun_data_altera' => 'Cli Data Altera',
            'fun_senha' => 'Senha',
            'fun_access_token' => "Access Token",
            'fun_auth_key' => "Auth Key"
        ];
    }

    public static function findIdentity($id){
        return self::findOne($id);
    }

    public static function findIdentityByAccessToken($token, $type = null){
        return self::findOne(['fun_access_token' => $token]);
    }

    public static function findByEmail($email){
        return self::findOne(['fun_email' => $email]);
    }

    public function getId(){
        return $this->fun_id;
    }

    public function getAuthKey(){
        // return $this->fun_auth_key;
        return null;
    }

    public static function getPrimeiroNome($nomeCompleto){
        $aNomeCompleto = explode(' ', $nomeCompleto);
        return $aNomeCompleto[0];
    }

    public function validateAuthKey($authKey){
        // return $this->fun_auth_key;
        return null;
    }

    public function validatePassword($passWord){
        return password_verify($passWord, $this->fun_senha);
    }

    
    /**
     * Gets query for [[Systems]].
     *
     * @return \yii\db\ActiveQuery|SystemQuery
     */
    public function getSystems()
    {
        return $this->hasMany(System::className(), ['sys_cliente' => 'fun_id']);
    }

    /**
     * {@inheritdoc}
     * @return queries\FuncionariosQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new queries\FuncionariosQuery(get_called_class());
    }
}
