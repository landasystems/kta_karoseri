<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "autentikasi_bbk".
 *
 * @property string $no_wo
 * @property string $tgl
 * @property string $kd_kerja
 * @property string $kd_barang
 * @property integer $jml
 * @property string $ket
 * @property integer $status
 */
class AutentikasiBbk extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'autentikasi_bbk';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['tgl'], 'safe'],
            [['jml', 'status'], 'integer'],
            [['no_wo'], 'string', 'max' => 20],
            [['kd_kerja', 'kd_barang'], 'string', 'max' => 10],
            [['ket'], 'string', 'max' => 50]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'no_wo' => 'No Wo',
            'tgl' => 'Tgl',
            'kd_kerja' => 'Kd Kerja',
            'kd_barang' => 'Kd Barang',
            'jml' => 'Jml',
            'ket' => 'Ket',
            'status' => 'Status',
        ];
    }
}
