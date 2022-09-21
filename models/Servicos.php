<?php

namespace app\models;

use app\helpers\Formatter;
use Yii;

/**
 * This is the model class for table "servicos".
 *
 * @property int $svs_id
 * @property float $svs_preco
 * @property string $svs_duracao
 * @property int|null $svs_retorno
 * @property int $svs_ativo
 * @property int $svs_system
 * @property string|null $svs_descricao
 * @property int $svs_excluido
 * @property string $svs_data_inclusao
 *
 * @property System $svsSystem
 */
class Servicos extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'servicos';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['svs_nome', 'svs_preco', 'svs_duracao', 'svs_ativo', 'svs_system'], 'required', 'message'=> 'Campo obrigatório'],
            [['svs_preco'], 'number'],
            [['svs_retorno', 'svs_ativo', 'svs_system', 'svs_excluido'], 'integer'],
            [['svs_descricao'], 'string'],
            [['svs_data_inclusao'], 'safe'],
            [['svs_nome', 'svs_duracao'], 'string', 'max' => 150],
            [['svs_system'], 'exist', 'skipOnError' => true, 'targetClass' => System::className(), 'targetAttribute' => ['svs_system' => 'sys_id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'svs_id' => 'Svs ID',
            'svs_nome' => 'Nome',
            'svs_preco' => 'Svs Preco',
            'svs_duracao' => 'Svs Duracao',
            'svs_retorno' => 'Svs Retorno',
            'svs_ativo' => 'Svs Ativo',
            'svs_system' => 'Svs System',
            'svs_descricao' => 'Sys Descricao',
            'svs_excluido' => 'Sys Excluido',
            'svs_data_inclusao' => 'Sys Data Inclusao',
        ];
    }


    /**
     * Altera os dados para mostrar corretamente no front end
     * @param bool $one True caso seja apenas um registro
     */
    public static function formatarParaRetorno($registros, $one = false){
        if(!empty($registros)){
            if(!$one){
                foreach($registros as $key => $registro){
                    $registros[$key]['svs_preco'] = Formatter::floatParaReal($registro['svs_preco']);
                }
            }else{
                $registros['svs_preco'] = Formatter::floatParaReal($registros['svs_preco']);
            }

            return $registros;
        }
    }

    public static function servicosPadroes($idSistema){
        return [
            [
                'svs_nome' => 'Progressiva',
                'svs_preco' => 150.0,
                'svs_duracao' => '01:30',
                'svs_retorno' => 10,
                'svs_ativo' => 1,
                'svs_system' => $idSistema,
                'svs_descricao' => '',
                'svs_excluido' => 0,
                'svs_data_inclusao' => date('Y-m-d')
            ],

            [
                'svs_nome' => 'Degradê',
                'svs_preco' => 45.0,
                'svs_duracao' => '00:30',
                'svs_retorno' => 30,
                'svs_ativo' => 1,
                'svs_system' => $idSistema,
                'svs_descricao' => '',
                'svs_excluido' => 0,
                'svs_data_inclusao' => date('Y-m-d')
            ],

            [
                'svs_nome' => 'Sobrancelha',
                'svs_preco' => 30.0,
                'svs_duracao' => '00:15',
                'svs_retorno' => 20,
                'svs_ativo' => 1,
                'svs_system' => $idSistema,
                'svs_descricao' => '',
                'svs_excluido' => 0,
                'svs_data_inclusao' => date('Y-m-d')
            ],

            [
                'svs_nome' => 'Barba',
                'svs_preco' => 50.0,
                'svs_duracao' => '00:30',
                'svs_retorno' => 30,
                'svs_ativo' => 1,
                'svs_system' => $idSistema,
                'svs_descricao' => '',
                'svs_excluido' => 0,
                'svs_data_inclusao' => date('Y-m-d')
            ],

            [
                'svs_nome' => 'Luzes',
                'svs_preco' => 100.0,
                'svs_duracao' => '00:50',
                'svs_retorno' => 10,
                'svs_ativo' => 1,
                'svs_system' => $idSistema,
                'svs_descricao' => '',
                'svs_excluido' => 0,
                'svs_data_inclusao' => date('Y-m-d')
            ],
        ];

        
    }

    /**
     * Gets query for [[SvsSystem]].
     *
     * @return \yii\db\ActiveQuery|SystemQuery
     */
    public function getSvsSystem()
    {
        return $this->hasOne(System::className(), ['sys_id' => 'svs_system']);
    }

    /**
     * {@inheritdoc}
     * @return queries\ServicosQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new queries\ServicosQuery(get_called_class());
    }
}
