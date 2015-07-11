<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "trans_uji_mutu".
 *
 * @property string $kd_uji
 * @property string $tgl
 * @property double $biaya_admin
 * @property double $total_biaya
 */
class Ujimutu extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'trans_uji_mutu';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['tgl'], 'safe'],
            [['biaya_admin', 'total_biaya'], 'number'],
            [['kd_uji'], 'string', 'max' => 10]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'kd_uji' => 'Kd Uji',
            'tgl' => 'Tgl',
            'biaya_admin' => 'Biaya Admin',
            'total_biaya' => 'Total Biaya',
        ];
    }
}
