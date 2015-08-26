<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "serah_terima_in".
 *
 * @property string $kd_titipan
 * @property string $no_spk
 * @property string $tgl_terima
 * @property string $kd_cust
 * @property string $driver
 * @property string $serah_terima
 * @property string $tgl_prd
 * @property string $tgl_pdc
 * @property string $kd_chassis
 * @property string $kd_model
 * @property string $no_chassis
 * @property string $no_mesin
 * @property string $kd_warna
 * @property integer $status
 */
class Serahterimain extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'serah_terima_in';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['kd_titipan'], 'required'],
            [['tgl_terima', 'serah_terima', 'tgl_prd', 'tgl_pdc'], 'safe'],
            [['status'], 'integer'],
            [['kd_titipan', 'no_spk'], 'string', 'max' => 10],
            [['kd_cust'], 'string', 'max' => 8],
            [['driver', 'kd_model'], 'string', 'max' => 50],
            [['kd_chassis', 'no_chassis', 'no_mesin', 'kd_warna'], 'string', 'max' => 20],
            [['kd_titipan'], 'unique']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'kd_titipan' => 'Kd Titipan',
            'no_spk' => 'No Spk',
            'tgl_terima' => 'Tgl Terima',
            'kd_cust' => 'Kd Cust',
            'driver' => 'Driver',
            'serah_terima' => 'Serah Terima',
            'tgl_prd' => 'Tgl Prd',
            'tgl_pdc' => 'Tgl Pdc',
            'kd_chassis' => 'Kd Chassis',
            'kd_model' => 'Kd Model',
            'no_chassis' => 'No Chassis',
            'no_mesin' => 'No Mesin',
            'kd_warna' => 'Kd Warna',
            'status' => 'Status',
        ];
    }
    public function getWarna() {
        return $this->hasOne(Warna::className(), ['kd_warna' => 'kd_warna']);
    }
}
