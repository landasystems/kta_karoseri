<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;
use yii\behaviors\SluggableBehavior;
use yii\behaviors\BlameableBehavior;

/**
 * This is the model class for table "retur_bbm".
 *
 * @property string $no_retur_bbm
 * @property string $tgl
 * @property string $no_bbm
 * @property string $surat_jalan
 * @property string $kd_supplier
 * @property string $kd_barang
 * @property double $jml
 * @property string $alasan
 * @property string $ket
 */
class Returbbm extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'retur_bbm';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['no_retur_bbm'], 'required'],
            [['tgl'], 'safe'],
            [['jml'], 'number'],
            [['no_retur_bbm', 'surat_jalan', 'alasan'], 'string', 'max' => 20],
            [['no_bbm'], 'string', 'max' => 15],
            [['kd_supplier', 'kd_barang'], 'string', 'max' => 10],
            [['ket'], 'string', 'max' => 300],
            [['no_retur_bbm'], 'unique']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'no_retur_bbm' => 'No Retur Bbm',
            'tgl' => 'Tgl',
            'no_bbm' => 'No Bbm',
            'surat_jalan' => 'Surat Jalan',
            'kd_supplier' => 'Kd Supplier',
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
