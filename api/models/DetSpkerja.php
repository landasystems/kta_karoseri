<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "det_spkerja".
 *
 * @property string $no_wo
 * @property string $kd_jab
 * @property string $kd_ker
 * @property double $qty
 * @property integer $no
 */
class DetSpkerja extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'det_spkerja';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['qty'], 'number'],
            [['no_wo', 'kd_ker'], 'string', 'max' => 10],
            [['kd_jab'], 'string', 'max' => 20]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'no_wo' => 'No Wo',
            'kd_jab' => 'Kd Jab',
            'kd_ker' => 'Kd Ker',
            'qty' => 'Qty',
            'no' => 'No',
        ];
    }
}
