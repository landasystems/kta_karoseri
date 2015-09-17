<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "rubah_bentuk".
 *
 * @property string $kd_rubah
 * @property string $no_wo
 * @property string $warna_baru
 * @property string $bentuk_baru
 * @property string $ket
 * @property string $tgl
 * @property string $pengajuan
 * @property string $terima
 * @property string $register_um
 * @property string $no_garansi
 * @property string $garansi
 */
class RubahBentuk extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'rubah_bentuk';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['tgl', 'pengajuan', 'terima','id'], 'safe'],
            [['kd_rubah', 'warna_baru', 'no_garansi'], 'string', 'max' => 30],
            [['no_wo'], 'string', 'max' => 10],
            [['bentuk_baru'], 'string', 'max' => 100],
            [['ket', 'register_um'], 'string', 'max' => 50],
            [['garansi'], 'string', 'max' => 20]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'kd_rubah' => 'Kd Rubah',
            'no_wo' => 'No Wo',
            'warna_baru' => 'Warna Baru',
            'bentuk_baru' => 'Bentuk Baru',
            'ket' => 'Ket',
            'tgl' => 'Tgl',
            'pengajuan' => 'Pengajuan',
            'terima' => 'Terima',
            'register_um' => 'Register Um',
            'no_garansi' => 'No Garansi',
            'garansi' => 'Garansi',
        ];
    }
}
