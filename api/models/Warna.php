<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "warna".
 *
 * @property string $kd_warna
 * @property string $warna
 */
class Warna extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'warna';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['kd_warna'], 'string', 'max' => 20],
            [['warna'], 'string', 'max' => 50]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'kd_warna' => 'Kd Warna',
            'warna' => 'Warna',
        ];
    }
}
