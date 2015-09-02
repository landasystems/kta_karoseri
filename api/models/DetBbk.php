<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "det_bbk".
 *
 * @property string $no_bbk
 * @property string $kd_barang
 * @property double $jml
 * @property string $ket
 */
class DetBbk extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'det_bbk';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['jml'], 'number'],
            [['no_bbk', 'kd_barang'], 'string', 'max' => 10],
            [['ket'], 'string', 'max' => 200]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'no_bbk' => 'No Bbk',
            'kd_barang' => 'Kd Barang',
            'jml' => 'Jml',
            'ket' => 'Ket',
        ];
    }
    
    public function getBarang() {
        return $this->hasOne(Barang::className(), ['kd_barang' => 'kd_barang']);
    }
    
}
