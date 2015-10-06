<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "jml_laporan".
 *
 * @property integer $id
 * @property string $nm_laporan
 * @property integer $jumlah
 */
class JmlLaporan extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'jml_laporan';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['nm_laporan'], 'required'],
            [['jumlah'], 'integer'],
            [['nm_laporan'], 'string', 'max' => 255]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'nm_laporan' => 'Nm Laporan',
            'jumlah' => 'Jumlah',
        ];
    }
}
