<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "chassis".
 *
 * @property string $kd_chassis
 * @property string $merk
 * @property string $tipe
 * @property string $jenis
 * @property string $wheelbase
 * @property string $model_chassis
 */
class Chassis extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'chassis';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['kd_chassis'], 'required'],
            [['kd_chassis', 'jenis'], 'string', 'max' => 20],
            [['merk', 'tipe'], 'string', 'max' => 30],
            [['wheelbase'], 'string', 'max' => 10],
            [['model_chassis'], 'string', 'max' => 50],
            [['kd_chassis'], 'unique']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'kd_chassis' => 'Kd Chassis',
            'merk' => 'Merk',
            'tipe' => 'Tipe',
            'jenis' => 'Jenis',
            'wheelbase' => 'Wheelbase',
            'model_chassis' => 'Model Chassis',
        ];
    }
}
