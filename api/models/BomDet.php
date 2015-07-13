<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "det_standar_bahan".
 *
 * @property string $kd_bom
 * @property string $kd_barang
 * @property double $qty
 * @property string $ket
 * @property string $kd_jab
 */
class BomDet extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'det_standar_bahan';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['kd_bom', 'qty'], 'required'],
            [['qty'], 'number'],
            [['kd_bom', 'kd_jab'], 'string', 'max' => 20],
            [['kd_barang'], 'string', 'max' => 10],
            [['ket'], 'string', 'max' => 1000]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'kd_bom' => 'Kd Bom',
            'kd_barang' => 'Kd Barang',
            'qty' => 'Qty',
            'ket' => 'Ket',
            'kd_jab' => 'Kd Jab',
        ];
    }
    
    public function getJabatan() {
        return $this->hasOne(Jabatan::className(), ['id_jabatan' => 'kd_jab']);
    }
    public function getBarang() {
        return $this->hasOne(Barang::className(), ['kd_barang' => 'kd_barang']);
    }
}
