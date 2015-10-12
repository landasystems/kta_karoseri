<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;
use yii\behaviors\SluggableBehavior;
use yii\behaviors\BlameableBehavior;

/**
 * This is the model class for table "det_claim".
 *
 * @property integer $id
 * @property string $no_wo
 * @property string $tgl
 * @property string $kd_jns
 * @property string $problem
 * @property string $solusi
 * @property string $tgl_pelaksanaan
 * @property string $pelaksana
 * @property double $biaya_mat
 * @property double $biaya_tk
 * @property double $biaya_spd
 */
class DetClaim extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'det_claim';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['tgl', 'tgl_pelaksanaan'], 'safe'],
            [['biaya_mat', 'biaya_tk', 'biaya_spd'], 'number'],
            [['no_wo'], 'string', 'max' => 20],
            [['kd_jns'], 'string', 'max' => 10],
            [['problem', 'solusi', 'pelaksana'], 'string', 'max' => 255]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'no_wo' => 'No Wo',
            'tgl' => 'Tgl',
            'kd_jns' => 'Kd Jns',
            'problem' => 'Problem',
            'solusi' => 'Solusi',
            'tgl_pelaksanaan' => 'Tgl Pelaksanaan',
            'pelaksana' => 'Pelaksana',
            'biaya_mat' => 'Biaya Mat',
            'biaya_tk' => 'Biaya Tk',
            'biaya_spd' => 'Biaya Spd',
        ];
    }
    
    public function behaviors() {
        return [
            [
                'class' => BlameableBehavior::className(),
                'createdByAttribute' => 'created_by',
                'updatedByAttribute' => 'modified_by',
            ],
            'timestamp' => [
                'class' => 'yii\behaviors\TimestampBehavior',
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['created_at', 'modified_at'],
                    ActiveRecord::EVENT_BEFORE_UPDATE => ['modified_at'],
                ],
            ],
        ];
    }
}
