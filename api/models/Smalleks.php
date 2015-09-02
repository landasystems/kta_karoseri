<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "small_eks".
 *
 * @property string $no_wo
 * @property string $plat_body
 * @property string $ventilasi_atas
 * @property string $kaca_spion
 * @property string $kaca_depan
 * @property string $kaca_belakang
 * @property string $kaca_samping
 * @property string $lampu_depan
 * @property string $lampu_belakang
 * @property string $pintu_depan
 * @property string $pintu_penumpang
 * @property string $pintu_bagasi_samping
 * @property string $pintu_belakang
 * @property string $wyper_set
 * @property string $anti_karat
 * @property string $warna
 * @property string $warna2
 * @property string $strip
 * @property string $letter
 * @property string $lain2
 */
class Smalleks extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'small_eks';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['lain2'], 'string'],
            [['no_wo'], 'string', 'max' => 20],
            [['plat_body', 'ventilasi_atas', 'kaca_spion', 'kaca_depan', 'kaca_belakang', 'kaca_samping', 'lampu_depan', 'lampu_belakang', 'pintu_depan', 'pintu_penumpang', 'pintu_bagasi_samping', 'pintu_belakang', 'wyper_set', 'anti_karat', 'warna', 'warna2', 'strip', 'letter'], 'string', 'max' => 100]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'no_wo' => 'No Wo',
            'plat_body' => 'Plat Body',
            'ventilasi_atas' => 'Ventilasi Atas',
            'kaca_spion' => 'Kaca Spion',
            'kaca_depan' => 'Kaca Depan',
            'kaca_belakang' => 'Kaca Belakang',
            'kaca_samping' => 'Kaca Samping',
            'lampu_depan' => 'Lampu Depan',
            'lampu_belakang' => 'Lampu Belakang',
            'pintu_depan' => 'Pintu Depan',
            'pintu_penumpang' => 'Pintu Penumpang',
            'pintu_bagasi_samping' => 'Pintu Bagasi Samping',
            'pintu_belakang' => 'Pintu Belakang',
            'wyper_set' => 'Wyper Set',
            'anti_karat' => 'Anti Karat',
            'warna' => 'Warna',
            'warna2' => 'Warna2',
            'strip' => 'Strip',
            'letter' => 'Letter',
            'lain2' => 'Lain2',
        ];
    }
    public function getWaarna(){
        return $this->hasOne(Warna::className(), ['kd_warna' => 'warna']);
    }
}
