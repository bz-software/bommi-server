<?php

namespace app\models;

use phpDocumentor\Reflection\Types\Self_;
use Yii;

/**
 * This is the model class for table "avatar".
 *
 * @property int $avt_id
 * @property int $avt_sys_id
 * @property string $avt_caminho
 * @property string $avt_data
 * @property int $avt_atual
 *
 * @property System $avtSys
 */
class Avatar extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'avatar';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['avt_sys_id', 'avt_caminho', 'avt_data'], 'required'],
            [['avt_sys_id', 'avt_atual'], 'integer'],
            [['avt_caminho', 'avt_data'], 'string', 'max' => 150],
            [['avt_sys_id'], 'exist', 'skipOnError' => true, 'targetClass' => System::className(), 'targetAttribute' => ['avt_sys_id' => 'sys_id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'avt_id' => 'Avt ID',
            'avt_sys_id' => 'Avt Sys ID',
            'avt_caminho' => 'Avt Caminho',
            'avt_data' => 'Avt Data',
            'avt_atual' => 'Avt Atual',
        ];
    }

    public static function setTodosNaoAtual($idSistema){
        self::updateAll([
            'avt_atual' => 0,
        ],
        [
            'avt_sys_id' => $idSistema
        ]);
    }

    public static function atual($idSistema){
        $avatar = self::find()->where(['avt_sys_id' => $idSistema])
                  ->andWhere(['avt_atual' => 1])->asArray()->one();

        return !empty($avatar) ? $avatar['avt_caminho'] : null;
    }   

    /**
     * Gets query for [[AvtSys]].
     *
     * @return \yii\db\ActiveQuery|SystemQuery
     */
    public function getSystem()
    {
        return $this->hasOne(System::className(), ['sys_id' => 'avt_sys_id']);
    }

    /**
     * {@inheritdoc}
     * @return queries\AvatarQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new queries\AvatarQuery(get_called_class());
    }
}
