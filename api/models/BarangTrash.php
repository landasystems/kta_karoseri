<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "barang_trash".
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
 * @property string $tgl_hapus
 * @property integer $user_id
 */
class BarangTrash extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'barang_trash';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['kd_barang', 'user_id'], 'required'],
            [['harga', 'max', 'min', 'saldo', 'qty'], 'number'],
            [['umur', 'status', 'user_id'], 'integer'],
            [['tgl_beli', 'tgl_hapus'], 'safe'],
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
            'tgl_hapus' => 'Tgl Hapus',
            'user_id' => 'User ID',
        ];
    }
}
