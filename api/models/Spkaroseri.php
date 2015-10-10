<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;
use yii\behaviors\SluggableBehavior;
use yii\behaviors\BlameableBehavior;

/**
 * This is the model class for table "spk".
 *
 * @property string $no_spk
 * @property string $tgl
 * @property string $kd_customer
 * @property integer $sumber_pelanggan
 * @property integer $pkp
 * @property string $npwp
 * @property integer $pemungut_pajak
 * @property string $kd_chassis
 * @property string $kd_model
 * @property integer $jarak_sumbu
 * @property integer $jml_hari
 * @property integer $jml_unit
 * @property double $harga_karoseri
 * @property double $harga_optional
 * @property double $jml_harga
 * @property double $ppn
 * @property double $total_harga
 * @property double $uang_muka
 * @property double $sisa_bayar
 * @property double $leasin
 * @property double $tenor_kredit
 * @property double $uang_muka_kredit
 * @property double $angsuran_bln
 * @property double $admin
 * @property double $asuransi
 * @property double $total_uang_muka_kredit
 * @property string $nik
 * @property string $kd_bom
 */
class Spkaroseri extends \yii\db\ActiveRecord {

    /**
     * @inheritdoc
     */
    public static function tableName() {
        return 'spk';
    }

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['no_spk'], 'required'],
            [['tgl'], 'safe'],
            [['sumber_pelanggan', 'pkp', 'pemungut_pajak', 'jarak_sumbu', 'jml_hari', 'jml_unit'], 'integer'],
            [['harga_karoseri', 'harga_optional', 'jml_harga', 'ppn', 'total_harga', 'uang_muka', 'sisa_bayar', 'leasin', 'tenor_kredit', 'uang_muka_kredit', 'angsuran_bln', 'admin', 'asuransi', 'total_uang_muka_kredit'], 'number'],
            [['no_spk'], 'string', 'max' => 10],
            [['kd_customer'], 'string', 'max' => 8],
            [['npwp', 'kd_chassis', 'nik', 'kd_bom'], 'string', 'max' => 20],
            [['kd_model'], 'string', 'max' => 5],
            [['no_spk'], 'unique']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'no_spk' => 'No Spk',
            'tgl' => 'Tgl',
            'kd_customer' => 'Kd Customer',
            'sumber_pelanggan' => 'Sumber Pelanggan',
            'pkp' => 'Pkp',
            'npwp' => 'Npwp',
            'pemungut_pajak' => 'Pemungut Pajak',
            'kd_chassis' => 'Kd Chassis',
            'kd_model' => 'Kd Model',
            'jarak_sumbu' => 'Jarak Sumbu',
            'jml_hari' => 'Jml Hari',
            'jml_unit' => 'Jml Unit',
            'harga_karoseri' => 'Harga Karoseri',
            'harga_optional' => 'Harga Optional',
            'jml_harga' => 'Jml Harga',
            'ppn' => 'Ppn',
            'total_harga' => 'Total Harga',
            'uang_muka' => 'Uang Muka',
            'sisa_bayar' => 'Sisa Bayar',
            'leasin' => 'Leasin',
            'tenor_kredit' => 'Tenor Kredit',
            'uang_muka_kredit' => 'Uang Muka Kredit',
            'angsuran_bln' => 'Angsuran Bln',
            'admin' => 'Admin',
            'asuransi' => 'Asuransi',
            'total_uang_muka_kredit' => 'Total Uang Muka Kredit',
            'nik' => 'Nik',
            'kd_bom' => 'Kd Bom',
        ];
    }

    public function getChassis() {
        return $this->hasOne(Chassis::className(), ['kd_chassis' => 'kd_chassis']);
    }

    public function getCustomer() {
        return $this->hasOne(Customer::className(), ['kd_cust' => 'kd_customer']);
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
