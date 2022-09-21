<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "cover".
 *
 * @property int $cov_id
 * @property int $cov_sys_id
 * @property string $cov_caminho
 * @property string $cov_data
 * @property int $cov_atual
 *
 * @property System $covSys
 */
class Cover extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'cover';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['cov_sys_id', 'cov_caminho', 'cov_data'], 'required'],
            [['cov_sys_id', 'cov_atual'], 'integer'],
            [['cov_caminho', 'cov_data'], 'string', 'max' => 150],
            [['cov_sys_id'], 'exist', 'skipOnError' => true, 'targetClass' => System::className(), 'targetAttribute' => ['cov_sys_id' => 'sys_id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'cov_id' => 'Cov ID',
            'cov_sys_id' => 'Cov Sys ID',
            'cov_caminho' => 'Cov Caminho',
            'cov_data' => 'Cov Data',
            'cov_atual' => 'Cov Atual',
        ];
    }

    public static function atual($idSistema){
        $cover = self::find()->where(['cov_sys_id' => $idSistema])
                  ->andWhere(['cov_atual' => 1])->asArray()->one();

        return !empty($cover) ? $cover['cov_caminho'] : null;
    }   

    public static function setTodosNaoAtual($idSistema){
        self::updateAll([
            'cov_atual' => 0,
        ],
        [
            'cov_sys_id' => $idSistema
        ]);
    }

    /**
     * Gets query for [[CovSys]].
     *
     * @return \yii\db\ActiveQuery|SystemQuery
     */
    public function getCovSys()
    {
        return $this->hasOne(System::className(), ['sys_id' => 'cov_sys_id']);
    }

    /**
     * {@inheritdoc}
     * @return queries\CoverQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new queries\CoverQuery(get_called_class());
    }
}
