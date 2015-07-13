<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "trans_po".
 *
 * @property string $nota
 * @property string $spp
 * @property string $tanggal
 * @property string $pemberi_order
 * @property string $suplier
 * @property integer $jatuh_tempo
 * @property string $dikirim_ke
 * @property double $total
 * @property double $diskon
 * @property double $ppn
 * @property double $total_dibayar
 * @property double $dp
 * @property double $sisa_dibayar
 * @property integer $status
 * @property integer $bayar
 */
class TransPo extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'trans_po';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['nota', 'dp'], 'required'],
            [['tanggal'], 'safe'],
            [['jatuh_tempo', 'status', 'bayar'], 'integer'],
            [['total', 'diskon', 'ppn', 'total_dibayar', 'dp', 'sisa_dibayar'], 'number'],
            [['nota', 'spp'], 'string', 'max' => 50],
            [['pemberi_order', 'suplier', 'dikirim_ke'], 'string', 'max' => 255],
            [['nota'], 'unique']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'nota' => 'Nota',
            'spp' => 'Spp',
            'tanggal' => 'Tanggal',
            'pemberi_order' => 'Pemberi Order',
            'suplier' => 'Suplier',
            'jatuh_tempo' => 'Jatuh Tempo',
            'dikirim_ke' => 'Dikirim Ke',
            'total' => 'Total',
            'diskon' => 'Diskon',
            'ppn' => 'Ppn',
            'total_dibayar' => 'Total Dibayar',
            'dp' => 'Dp',
            'sisa_dibayar' => 'Sisa Dibayar',
            'status' => 'Status',
            'bayar' => 'Bayar',
        ];
    }
     public function getDetailPo()
    {
        return $this->hasOne(DetailPo::className(), ['nota' => 'nota']);
    }
     public function getSupplier()
    {
        return $this->hasOne(Supplier::className(), ['kd_supplier' => 'supplier']);
    }
}
