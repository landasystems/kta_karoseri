<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "det_additional_bom".
 *
 * @property integer $id
 * @property string $kd_bom
 * @property string $kd_barang
 * @property double $qty
 * @property string $ket
 * @property string $kd_jab
 */
class DetAdditionalBom extends \yii\db\ActiveRecord {

    /**
     * @inheritdoc
     */
    public static function tableName() {
        return 'det_additional_bom';
    }

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['kd_bom', 'qty'], 'required'],
            [['qty','trans_additional_bom_id'], 'number'],
            [['kd_bom', 'kd_jab'], 'string', 'max' => 20],
            [['kd_barang'], 'string', 'max' => 10],
            [['ket'], 'string', 'max' => 1000]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'id' => 'ID',
            'kd_bom' => 'Kd Bom',
            'trans_additional_bom_id' => 'Additional Bom', 
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
    public function getAdditionalbom() {
        return $this->hasOne(TransAdditionalBom::className(), ['id' => 'trans_additional_bom_id']);
    }

}
