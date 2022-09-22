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
 *
 * @property UrlCadastroFuncionarios $ucfSystem
 * @property UrlCadastroFuncionarios[] $urlCadastroFuncionarios
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
            [['ucf_system', 'ucf_token', 'ucf_expire'], 'required'],
            [['ucf_system', 'ucf_usuario_cadastrado'], 'integer'],
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
            'ucf_id' => 'Ucf ID',
            'ucf_system' => 'Ucf System',
            'ucf_token' => 'Ucf Token',
            'ucf_expire' => 'Ucf Expire',
            'ucf_usuario_cadastrado' => 'Ucf Usuario Cadastrado',
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
     * {@inheritdoc}
     * @return queries\UrlCadastroFuncionariosQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new queries\UrlCadastroFuncionariosQuery(get_called_class());
    }
}
