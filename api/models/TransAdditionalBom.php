<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "trans_additional_bom".
 *
 * @property integer $id
 * @property string $no_wo
 * @property string $kd_chassis
 * @property string $kd_model
 * @property string $tgl_buat
 * @property integer $status
 * @property string $jenis
 * @property integer $umur
 * @property string $foto
 */
class TransAdditionalBom extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'trans_additional_bom';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['tgl_buat','foto'], 'safe'],
            [['status', 'umur'], 'integer'],
            [['no_wo', 'kd_chassis','kd_bom'], 'string', 'max' => 20],
            [['kd_model'], 'string', 'max' => 5],
            [['jenis'], 'string', 'max' => 10],
//            [[''], 'string', 'max' => 500]
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
            'kd_chassis' => 'Kd Chassis',
            'kd_model' => 'Kd Model',
            'tgl_buat' => 'Tgl Buat',
            'status' => 'Status',
            'jenis' => 'Jenis',
            'umur' => 'Umur',
            'foto' => 'Gambar',
        ];
    }
    
    public function getWo() {
        return $this->hasMany(TransAdditionalBomWo::className(), ['tran_additional_bom_id' => 'id']);
    }
}
