<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;
use yii\behaviors\SluggableBehavior;
use yii\behaviors\BlameableBehavior;

/**
 * This is the model class for table "wo_masuk".
 *
 * @property string $kd_titipan
 * @property string $no_spk
 * @property string $no_wo
 * @property integer $kondisi
 * @property string $in_spk_marketing
 * @property string $tgl_kontrak
 * @property integer $stat
 */
class WoMasuk extends \yii\db\ActiveRecord {

    /**
     * @inheritdoc
     */
    public static function tableName() {
        return 'wo_masuk';
    }

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['no_wo'], 'unique'],
            [['no_wo'], 'required'],
            [['kondisi', 'stat'], 'integer'],
            [['in_spk_marketing', 'tgl_kontrak', 'tgl_keluar'], 'safe'],
            [['kd_titipan', 'no_spk', 'no_wo', 'foto'], 'string', 'max' => 10],
            
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'kd_titipan' => 'Kd Titipan',
            'no_spk' => 'No Spk',
            'no_wo' => 'No Wo',
            'kondisi' => 'Kondisi',
            'in_spk_marketing' => 'In Spk Marketing',
            'tgl_kontrak' => 'Tgl Kontrak',
            'stat' => 'Stat',
        ];
    }
    public function getSpkaroseri() {
        return $this->hasOne(Spkaroseri::className(), ['no_spk' => 'no_spk']);
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
