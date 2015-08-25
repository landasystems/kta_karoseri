<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "det_bbm".
 *
 * @property string $no_bbm
 * @property string $tgl_terima
 * @property string $kd_barang
 * @property string $no_po
 * @property double $jumlah
 * @property string $keterangan
 */
class DetBbm extends \yii\db\ActiveRecord {

    /**
     * @inheritdoc
     */
    public static function tableName() {
        return 'det_bbm';
    }

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['id'], 'number'],
            [['tgl_terima'], 'safe'],
            [['jumlah'], 'number'],
            [['no_bbm'], 'string', 'max' => 15],
            [['kd_barang'], 'string', 'max' => 10],
            [['no_po'], 'string', 'max' => 50],
            [['keterangan'], 'string', 'max' => 255]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'no_bbm' => 'No Bbm',
            'tgl_terima' => 'Tgl Terima',
            'kd_barang' => 'Kd Barang',
            'no_po' => 'No Po',
            'jumlah' => 'Jumlah',
            'keterangan' => 'Keterangan',
        ];
    }

    public function getBarang() {
        return $this->hasOne(Barang::className(), ['kd_barang' => 'kd_barang']);
    }

}