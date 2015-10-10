<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;
use yii\behaviors\SluggableBehavior;
use yii\behaviors\BlameableBehavior;

/**
 * This is the model class for table "trans_spp".
 *
 * @property string $no_spp
 * @property string $tgl_trans
 * @property string $tgl1
 * @property string $tgl2
 * @property string $no_proyek
 * @property string $nm_proyek
 */
class TransSpp extends \yii\db\ActiveRecord {

    /**
     * @inheritdoc
     */
    public static function tableName() {
        return 'trans_spp';
    }

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['no_spp'], 'required'],
            [['tgl_trans'], 'safe'],
            [['no_spp'], 'string', 'max' => 7],
            [['tgl1', 'tgl2'], 'string', 'max' => 10],
            [['no_proyek', 'nm_proyek'], 'string', 'max' => 20],
            [['no_spp'], 'unique']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'no_spp' => 'No Spp',
            'tgl_trans' => 'Tgl Trans',
            'tgl1' => 'Tgl1',
            'tgl2' => 'Tgl2',
            'no_proyek' => 'No Proyek',
            'nm_proyek' => 'Nm Proyek',
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
