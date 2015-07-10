<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "det_uji_mutu".
 *
 * @property string $kd_uji
 * @property string $no_wo
 * @property integer $kelas
 * @property double $biaya
 * @property string $bentuk_baru
 */
class DetUjimutu extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'det_uji_mutu';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['kelas'], 'integer'],
            [['biaya'], 'number'],
            [['kd_uji', 'no_wo'], 'string', 'max' => 10],
            [['bentuk_baru'], 'string', 'max' => 100]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'kd_uji' => 'Kd Uji',
            'no_wo' => 'No Wo',
            'kelas' => 'Kelas',
            'biaya' => 'Biaya',
            'bentuk_baru' => 'Bentuk Baru',
        ];
    }
}
