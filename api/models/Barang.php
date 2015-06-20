<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "barang".
 *
 * @property string $kd_barang
 * @property string $nm_barang
 * @property string $jenis
 * @property double $harga
 * @property string $satuan
 * @property double $max
 * @property double $min
 * @property double $saldo
 * @property double $qty
 * @property string $kat
 * @property integer $umur
 * @property string $tgl_beli
 * @property string $foto
 * @property integer $status
 * @property string $tgl_pakai
 */
class Barang extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'barang';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['kd_barang'], 'required'],
            [['harga', 'max', 'min', 'saldo', 'qty'], 'number'],
            [['umur', 'status'], 'integer'],
            [['tgl_beli', 'tgl_pakai'], 'safe'],
            [['kd_barang', 'jenis', 'satuan'], 'string', 'max' => 10],
            [['nm_barang'], 'string', 'max' => 255],
            [['kat'], 'string', 'max' => 20],
            [['foto'], 'string', 'max' => 500],
            [['kd_barang'], 'unique']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'kd_barang' => 'Kd Barang',
            'nm_barang' => 'Nm Barang',
            'jenis' => 'Jenis',
            'harga' => 'Harga',
            'satuan' => 'Satuan',
            'max' => 'Max',
            'min' => 'Min',
            'saldo' => 'Saldo',
            'qty' => 'Qty',
            'kat' => 'Kat',
            'umur' => 'Umur',
            'tgl_beli' => 'Tgl Beli',
            'foto' => 'Foto',
            'status' => 'Status',
            'tgl_pakai' => 'Tgl Pakai',
        ];
    }
}
