<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "trans_standar_bahan".
 *
 * @property string $kd_bom
 * @property string $kd_chassis
 * @property string $kd_model
 * @property string $tgl_buat
 * @property integer $status
 * @property string $jenis
 * @property integer $umur
 * @property string $foto
 */
class Validasibom extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'trans_standar_bahan';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['kd_bom', 'kd_chassis', 'kd_model', 'tgl_buat'], 'required'],
            [['tgl_buat'], 'safe'],
            [['status', 'umur'], 'integer'],
            [['kd_bom', 'kd_chassis'], 'string', 'max' => 20],
            [['kd_model'], 'string', 'max' => 5],
            [['jenis'], 'string', 'max' => 10],
            [['foto'], 'string', 'max' => 500],
            [['kd_bom'], 'unique']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'kd_bom' => 'Kd Bom',
            'kd_chassis' => 'Kd Chassis',
            'kd_model' => 'Kd Model',
            'tgl_buat' => 'Tgl Buat',
            'status' => 'Status',
            'jenis' => 'Jenis',
            'umur' => 'Umur',
            'foto' => 'Gambar',
        ];
    }
}
