<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "trans_spkerja".
 *
 * @property string $no_wo
 * @property string $kd_jab
 * @property integer $status
 */
class Spk extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'trans_spkerja';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['status'], 'integer'],
            [['no_wo'], 'string', 'max' => 10],
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
            'status' => 'Status',
        ];
    }
}
