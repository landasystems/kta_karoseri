<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "det_wip".
 *
 * @property string $no_wo
 * @property string $kd_kerja
 * @property string $plan_start
 * @property string $plan_finish
 * @property string $act_start
 * @property string $act_finish
 * @property string $ket
 * @property string $nik
 */
class Wip extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'det_wip';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['act_finish'], 'safe'],
            [['keterangan'], 'string'],
            [['no_wo', 'nik'], 'string', 'max' => 20],
            [['kd_kerja'], 'string', 'max' => 7],
            [['plan_start', 'plan_finish', 'act_start'], 'string', 'max' => 10]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'no_wo' => 'No Wo',
            'kd_kerja' => 'Kd Kerja',
            'plan_start' => 'Plan Start',
            'plan_finish' => 'Plan Finish',
            'act_start' => 'Act Start',
            'act_finish' => 'Act Finish',
            'ket' => 'Ket',
            'nik' => 'Nik',
        ];
    }
}
