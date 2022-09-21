<?php

namespace app\models;

use app\helpers\ControllerHelper;
use Yii;
use yii\web\UploadedFile;
use app\models\Avatar;

/**
 * This is the model class for table "system".
 *
 * @property int $sys_id
 * @property string $sys_nome_empresa
 * @property string $sys_dominio
 * @property string $sys_telefone
 * @property string $sys_cep
 * @property string $sys_cidade
 * @property string $sys_uf
 * @property string $sys_bairro
 * @property string $sys_endereco
 * @property string $sys_numero
 * @property string $sys_complemento
 * @property string $sys_logo
 * @property string $sys_data_inicio
 * @property int $sys_excluido
 * @property int $sys_cliente
 * @property string $sys_cnpj
 * @property string $sys_capa
 * @property int $sys_principal
 * 
 * 
 * @property Avatar[] $avatars 
 * @property Cover[] $covers
 * @property Funcionarios[] $funcionarios 
 * @property Servicos[] $servicos 
 * @property Funcionarios $sysCliente 
 * @property UrlCadastroFuncionarios[] $urlCadastroFuncionarios 
 */
class System extends \yii\db\ActiveRecord
{

    public $avatar;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'system';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['sys_nome_empresa', 'sys_cnpj', 'sys_dominio', 'sys_telefone', 'sys_cep', 'sys_cidade', 'sys_uf', 'sys_bairro', 'sys_endereco'], 'required', 'message' => 'Campo obrigatório'],
            [['sys_data_inicio'], 'safe'],
            [['sys_excluido', 'sys_principal'], 'integer'],
            [['sys_nome_empresa', 'sys_cnpj', 'sys_dominio', 'sys_telefone', 'sys_cep', 'sys_cidade', 'sys_bairro', 'sys_endereco', 'sys_complemento', 'sys_logo', 'sys_capa'], 'string', 'max' => 150],
            [['sys_uf'], 'string', 'max' => 2],
            [['sys_numero'], 'string', 'max' => 3],
            [['avatar'], 'file', 'skipOnEmpty' => true, 'extensions' => 'png, jpg'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'sys_id' => 'id',
            'sys_nome_empresa' => 'nomeEmpresa',
            'sys_dominio' => 'dominio',
            'sys_telefone' => 'telefone',
            'sys_cep' => 'cep',
            'sys_cidade' => 'cidade',
            'sys_uf' => 'uf',
            'sys_bairro' => 'bairro',
            'sys_endereco' => 'endereco',
            'sys_numero' => 'numero',
            'sys_complemento' => 'complemento',
            'sys_logo' => 'logo',
            'sys_data_inicio' => 'dataInicio',
            'sys_excluido' => 'excluido',
            'sys_capa' => 'capa',
            'sys_principal' => 'principal'
        ];
    }

    public static function findByFuncionarioId($id){
        $sistema = self::find()->where(['sys_cliente' => $id])
        ->andWhere(['!=','sys_excluido', 1])
        ->andWhere(['sys_principal' => 1])
        ->asArray()->one();

        if(empty($sistema)){
            $sistema = self::find()->where(['sys_cliente' => $id])
            ->andWhere(['!=','sys_excluido', 1])->asArray()->one();
        }

        $sistema['sys_logo'] = !empty(Avatar::atual($sistema['sys_id'])) ? ControllerHelper::pathToSystemAvatar() . Avatar::atual($sistema['sys_id']) : $sistema['sys_logo'];
        $sistema['sys_capa'] = !empty(Cover::atual($sistema['sys_id'])) ? ControllerHelper::pathToSystemCover() . Cover::atual($sistema['sys_id']) : $sistema['sys_capa'];

        return $sistema;
    }

    public static function findByDominio($dominio){
        $sistema = self::find()->where(['sys_dominio' => $dominio])
        ->andWhere(['sys_excluido' => 0])
        ->andWhere(['sys_principal' => 1])
        ->asArray()->one();

        if(empty($sistema)){
            $sistema = self::find()->where(['sys_dominio' => $dominio])
            ->andWhere(['sys_excluido' => 0])
            ->asArray()->one();
        }

        if(!empty($sistema)){
            $sistema['sys_logo'] = !empty(Avatar::atual($sistema['sys_id'])) ? ControllerHelper::pathToSystemAvatar() . Avatar::atual($sistema['sys_id']) : $sistema['sys_logo'];
            $sistema['sys_capa'] = !empty(Cover::atual($sistema['sys_id'])) ? ControllerHelper::pathToSystemCover() . Cover::atual($sistema['sys_id']) : $sistema['sys_capa'];

            return $sistema;
        }else{
            return false;
        }
    }

    public static function findAllNaoAtivos($idSistemaAtivo, $idUsuario){
        $sistemas = self::find()->where(['sys_excluido' => 0])
        ->andWhere(['sys_principal' => 0])
        ->andWhere(['!=', 'sys_id', $idSistemaAtivo])
        ->andWhere(['sys_cliente' => $idUsuario])
        ->asArray()->all();

        return $sistemas;
    }

    public static function setPrincipal($idSistema, $idUsuario){
        self::updateAll(['sys_principal' => 0], ['sys_cliente' => $idUsuario]);

        $sistema = self::findOne($idSistema);
        $sistema->sys_principal = 1;
        $sistema->save();
    }

    /**
    * Gets query for [[Avatars]].
    *
    * @return \yii\db\ActiveQuery|AvatarQuery
    */
    public function getAvatars(){
        return $this->hasMany(Avatar::className(), ['avt_sys_id' => 'sys_id']);
    }

   /**
    * Gets query for [[Funcionarios]].
    *
    * @return \yii\db\ActiveQuery|FuncionariosQuery
    */
    public function getFuncionarios(){
        return $this->hasMany(Funcionarios::className(), ['fun_sys_id' => 'sys_id']);
    }

   /**
    * Gets query for [[Servicos]].
    *
    * @return \yii\db\ActiveQuery|ServicosQuery
    */
    public function getServicos(){
        return $this->hasMany(Servicos::className(), ['svs_system' => 'sys_id']);
    }

   /**
    * Gets query for [[SysCliente]].
    *
    * @return \yii\db\ActiveQuery|FuncionariosQuery
    */
    public function getSysCliente(){
        return $this->hasOne(Funcionarios::className(), ['fun_id' => 'sys_cliente']);
    }

   /**
    * Gets query for [[UrlCadastroFuncionarios]].
    *
    * @return \yii\db\ActiveQuery|UrlCadastroFuncionariosQuery
    */
    public function getUrlCadastroFuncionarios(){
        return $this->hasMany(UrlCadastroFuncionarios::className(), ['ucf_system' => 'sys_id']);
    }

    /**
     * {@inheritdoc}
     * @return queries\SystemQuery the active query used by this AR class.
    */
    public static function find(){
        return new queries\SystemQuery(get_called_class());
    }

}
