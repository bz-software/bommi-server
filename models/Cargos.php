<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "cargos".
 *
 * @property int $car_id
 * @property string $car_descricao
 * @property int $car_excluido
 * @property int|null $car_sys_id
 *
 * @property System $carSys
 * @property Funcionarios[] $funcionarios
 */
class Cargos extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'cargos';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['car_descricao'], 'required'],
            [['car_excluido', 'car_sys_id'], 'integer'],
            [['car_descricao'], 'string', 'max' => 250],
            [['car_sys_id'], 'exist', 'skipOnError' => true, 'targetClass' => System::className(), 'targetAttribute' => ['car_sys_id' => 'sys_id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'car_id' => 'Car ID',
            'car_descricao' => 'Car Descricao',
            'car_excluido' => 'Car Excluido',
            'car_sys_id' => 'Car Sys ID',
        ];
    }

    public static function porSistema($idSistema){
        return self::find()->where(['car_sys_id' => $idSistema])->andWhere(['car_excluido' => 0])->all();
    }

    /**
     * Gets query for [[CarSys]].
     *
     * @return \yii\db\ActiveQuery|SystemQuery
     */
    public function getCarSys()
    {
        return $this->hasOne(System::className(), ['sys_id' => 'car_sys_id']);
    }

    /**
     * Gets query for [[Funcionarios]].
     *
     * @return \yii\db\ActiveQuery|FuncionariosQuery
     */
    public function getFuncionarios()
    {
        return $this->hasMany(Funcionarios::className(), ['fun_car_id' => 'car_id']);
    }

    /**
     * {@inheritdoc}
     * @return queries\CargosQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new queries\CargosQuery(get_called_class());
    }
}
