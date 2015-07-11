<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "det_spp".
 *
 * @property integer $id
 * @property string $no_spp
 * @property string $kd_barang
 * @property double $saldo
 * @property double $qty
 * @property string $ket
 * @property string $p
 * @property string $a
 * @property integer $stat_spp
 * @property string $no_wo
 */
class DetSpp extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'det_spp';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['saldo', 'qty'], 'number'],
            [['p', 'a'], 'safe'],
            [['stat_spp'], 'integer'],
            [['no_spp'], 'string', 'max' => 7],
            [['kd_barang', 'no_wo'], 'string', 'max' => 10],
            [['ket'], 'string', 'max' => 1000]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'no_spp' => 'No Spp',
            'kd_barang' => 'Kd Barang',
            'saldo' => 'Saldo',
            'qty' => 'Qty',
            'ket' => 'Ket',
            'p' => 'P',
            'a' => 'A',
            'stat_spp' => 'Stat Spp',
            'no_wo' => 'No Wo',
        ];
    }
}
