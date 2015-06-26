<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "bstk".
 *
 * @property string $no_wo
 * @property string $tgl
 * @property string $catatan
 * @property string $kd_warna
 */
class Bstk extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'bstk';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['tgl'], 'safe'],
            [['catatan'], 'string'],
            [['kd_warna'], 'required'],
            [['no_wo'], 'string', 'max' => 10],
            [['kd_warna'], 'string', 'max' => 20]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'no_wo' => 'No Wo',
            'tgl' => 'Tgl',
            'catatan' => 'Catatan',
            'kd_warna' => 'Kd Warna',
        ];
    }
}
