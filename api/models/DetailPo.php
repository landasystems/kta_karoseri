<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "detail_po".
 *
 * @property string $nota
 * @property string $kd_barang
 * @property double $jml
 * @property double $harga
 * @property double $diterima
 * @property string $ket
 * @property string $tgl_pengiriman
 * @property integer $stat_po
 */
class DetailPo extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'detail_po';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['jml', 'harga', 'diterima'], 'number'],
            [['ket'], 'string'],
            [['tgl_pengiriman'], 'safe'],
            [['stat_po'], 'integer'],
            [['nota'], 'string', 'max' => 50],
            [['kd_barang'], 'string', 'max' => 255]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'nota' => 'Nota',
            'kd_barang' => 'Kd Barang',
            'jml' => 'Jml',
            'harga' => 'Harga',
            'diterima' => 'Diterima',
            'ket' => 'Ket',
            'tgl_pengiriman' => 'Tgl Pengiriman',
            'stat_po' => 'Stat Po',
        ];
    }
}
