<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;
use yii\behaviors\SluggableBehavior;
use yii\behaviors\BlameableBehavior;

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
class TransPo extends \yii\db\ActiveRecord {

    public $supplier, $kode;

    /**
     * @inheritdoc
     */
    public static function tableName() {
        return 'trans_po';
    }

    /**
     * @inheritdoc
     */
    public function rules() {
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
    public function attributeLabels() {
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

    public function getDetailPo() {
        return $this->hasOne(DetailPo::className(), ['nota' => 'nota']);
    }

    public function getTransSpp() {
        return $this->hasOne(TransSpp::className(), ['no_spp' => 'spp']);
    }

    public function getSupplier() {
        return $this->hasMany(Supplier::className(), ['kd_supplier' => 'suplier']);
    }

    public function behaviors() {
        return [
            [
                'class' => BlameableBehavior::className(),
                'createdByAttribute' => 'created_by',
                'updatedByAttribute' => 'modified_by',
            ],
            'timestamp' => [
                'class' => 'yii\behaviors\TimestampBehavior',
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['created_at', 'modified_at'],
                    ActiveRecord::EVENT_BEFORE_UPDATE => ['modified_at'],
                ],
            ],
        ];
    }

}
