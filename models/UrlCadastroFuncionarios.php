<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "url_cadastro_funcionarios".
 *
 * @property int $ucf_id
 * @property int $ucf_system
 * @property string $ucf_token
 * @property string $ucf_expire
 * @property int $ucf_usuario_cadastrado
 * @property int $ufc_car_id
 *
 * @property UrlCadastroFuncionarios $ucfSystem
 * @property UrlCadastroFuncionarios[] $urlCadastroFuncionarios
 * @property Cargos $ufcCar
 * 
 */
class UrlCadastroFuncionarios extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'url_cadastro_funcionarios';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['ucf_system', 'ucf_token', 'ucf_expire', 'ufc_car_id'], 'required'],
            [['ucf_system', 'ucf_usuario_cadastrado', 'ufc_car_id'], 'integer'],
            [['ucf_token', 'ucf_expire'], 'string', 'max' => 150],
            [['ucf_system'], 'exist', 'skipOnError' => true, 'targetClass' => System::className(), 'targetAttribute' => ['ucf_system' => 'sys_id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'ucf_id' => 'Id',
            'ucf_system' => 'Sistema',
            'ucf_token' => 'Código de cadastro',
            'ucf_expire' => 'Validade',
            'ucf_usuario_cadastrado' => 'Usuário',
            'ufc_car_id' => 'Cargo',
        ];
    }

    public static function disponiveis($id){
        $urlDisponiveis = self::find()
                          ->where(['ucf_system' => $id])
                          ->andWhere(['IS', 'ucf_usuario_cadastrado', null ])
                          ->andWhere(['>', 'ucf_expire', strtotime(date('Y-m-d H:i:s'))])
                          ->all();

        return $urlDisponiveis;
    }

    public static function totalDisponivel($id){
        $urlDisponiveis = self::find()
                          ->where(['ucf_system' => $id])
                          ->andWhere(['IS', 'ucf_usuario_cadastrado', null ])
                          ->andWhere(['>', 'ucf_expire', strtotime(date('Y-m-d H:i:s'))])
                          ->count();

        return $urlDisponiveis;
    }

    /**
     * Gets query for [[UcfSystem]].
     *
     * @return \yii\db\ActiveQuery|UrlCadastroFuncionariosQuery
     */
    public function getUcfSystem()
    {
        return $this->hasOne(UrlCadastroFuncionarios::className(), ['ucf_id' => 'ucf_system']);
    }

    /**
     * Gets query for [[UrlCadastroFuncionarios]].
     *
     * @return \yii\db\ActiveQuery|UrlCadastroFuncionariosQuery
     */
    public function getUrlCadastroFuncionarios()
    {
        return $this->hasMany(UrlCadastroFuncionarios::className(), ['ucf_system' => 'ucf_id']);
    }

    /**
    * Gets query for [[UfcCar]].
    *
    * @return \yii\db\ActiveQuery|queries\CargosQuery
    */
    public function getUfcCar()
    {
        return $this->hasOne(Cargos::className(), ['car_id' => 'ufc_car_id']);
    }

    /**
     * {@inheritdoc}
     * @return queries\UrlCadastroFuncionariosQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new queries\UrlCadastroFuncionariosQuery(get_called_class());
    }
}
