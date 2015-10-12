<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;
use yii\behaviors\SluggableBehavior;
use yii\behaviors\BlameableBehavior;

/**
 * This is the model class for table "trans_bbm".
 *
 * @property string $no_bbm
 * @property string $tgl_nota
 * @property string $surat_jalan
 * @property string $kd_suplier
 * @property string $pengirim
 * @property string $penerima
 * @property string $no_wo
 */
class TransBbm extends \yii\db\ActiveRecord {

    /**
     * @inheritdoc
     */
    public static function tableName() {
        return 'trans_bbm';
    }

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['no_bbm'], 'required'],
            [['tgl_nota'], 'safe'],
            [['no_bbm', 'no_po'], 'string', 'max' => 15],
            [['surat_jalan'], 'string', 'max' => 20],
            [['kd_suplier', 'no_wo'], 'string', 'max' => 10],
            [['pengirim', 'penerima'], 'string', 'max' => 50],
            [['no_bbm'], 'unique']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'no_bbm' => 'No Bbm',
            'tgl_nota' => 'Tgl Nota',
            'surat_jalan' => 'Surat Jalan',
            'kd_suplier' => 'Kd Suplier',
            'pengirim' => 'Pengirim',
            'penerima' => 'Penerima',
            'no_wo' => 'No Wo',
        ];
    }

    public function getSupplier() {
        return $this->hasOne(Supplier::className(), ['kd_supplier' => 'kd_suplier']);
    }

    public function getPo() {
        return $this->hasOne(TransPo::className(), ['nota' => 'no_po']);
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
