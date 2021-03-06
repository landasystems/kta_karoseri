<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;
use yii\behaviors\SluggableBehavior;
use yii\behaviors\BlameableBehavior;

/**
 * This is the model class for table "retur_bbk".
 *
 * @property string $no_retur_bbk
 * @property string $tgl
 * @property string $no_bbk
 * @property string $kd_barang
 * @property double $jml
 * @property string $alasan
 * @property string $ket
 */
class ReturBbk extends \yii\db\ActiveRecord {

    /**
     * @inheritdoc
     */
    public static function tableName() {
        return 'retur_bbk';
    }

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['no_retur_bbk'], 'required'],
            [['tgl'], 'safe'],
            [['jml'], 'number'],
            [['no_retur_bbk', 'alasan'], 'string', 'max' => 20],
            [['no_bbk', 'kd_barang'], 'string', 'max' => 10],
            [['ket'], 'string', 'max' => 300],
            [['no_retur_bbk'], 'unique']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'no_retur_bbk' => 'No Retur Bbk',
            'tgl' => 'Tgl',
            'no_bbk' => 'No Bbk',
            'kd_barang' => 'Kd Barang',
            'jml' => 'Jml',
            'alasan' => 'Alasan',
            'ket' => 'Ket',
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
