<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "delivery".
 *
 * @property string $no_wo
 * @property string $tgl_delivery
 * @property string $no_delivery
 * @property integer $status
 * @property string $tujuan
 * @property string $driver
 */
class Delivery extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'delivery';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['tgl_delivery','foto'], 'safe'],
            [['status'], 'integer'],
            [['no_wo'], 'string', 'max' => 10],
            [['no_delivery'], 'string', 'max' => 20],
            [['tujuan','cabang','kd_cust'], 'string', 'max' => 500],
            [['driver'], 'string', 'max' => 100]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'no_wo' => 'No Wo',
            'tgl_delivery' => 'Tgl Delivery',
            'no_delivery' => 'No Delivery',
            'status' => 'Status',
            'tujuan' => 'Tujuan',
            'driver' => 'Driver',
        ];
    }
}
