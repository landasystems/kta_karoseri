<?php

namespace app\models;
use Yii;
use yii\db\ActiveRecord;
use yii\behaviors\SluggableBehavior;
use yii\behaviors\BlameableBehavior;

/**
 * This is the model class for table "trans_standar_bahan".
 *
 * @property string $kd_bom
 * @property string $kd_chassis
 * @property string $kd_model
 * @property string $tgl_buat
 * @property integer $status
 * @property string $jenis
 * @property integer $umur
 * @property string $foto
 */
class Bom extends \yii\db\ActiveRecord {

    /**
     * @inheritdoc
     */
    public static function tableName() {
        return 'trans_standar_bahan';
    }

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['kd_bom', 'kd_chassis', 'kd_model', 'tgl_buat'], 'required'],
            [['tgl_buat', 'foto'], 'safe'],
            [['status', 'umur'], 'integer'],
            [['kd_bom', 'kd_chassis'], 'string', 'max' => 20],
            [['kd_model'], 'string', 'max' => 5],
            [['jenis'], 'string', 'max' => 10],
//            [['foto'], 'string', 'max' => 500],
            [['kd_bom'], 'unique']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'kd_bom' => 'Kd Bom',
            'kd_chassis' => 'Kd Chassis',
            'kd_model' => 'Kd Model',
            'tgl_buat' => 'Tgl Buat',
            'status' => 'Status',
            'jenis' => 'Jenis',
            'umur' => 'Umur',
            'foto' => 'Gambar',
        ];
    }

    public function getChassis() {
        return $this->hasOne(Barang::className(), ['kd_chassis' => 'kd_chassis']);
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
