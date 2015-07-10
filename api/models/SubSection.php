<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "pekerjaan".
 *
 * @property string $kd_kerja
 * @property string $kerja
 * @property string $id_section
 *
 * @property TblSection $idSection
 * @property TblJabatan[] $tblJabatans
 */
class SubSection extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'pekerjaan';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['kd_kerja'], 'required'],
            [['kd_kerja'], 'string', 'max' => 10],
            [['kerja', 'id_section'], 'string', 'max' => 20],
            [['kd_kerja'], 'unique']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'kd_kerja' => 'Kd Kerja',
            'kerja' => 'Kerja',
            'id_section' => 'Id Section',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getIdSection()
    {
        return $this->hasOne(TblSection::className(), ['id_section' => 'id_section']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTblJabatans()
    {
        return $this->hasMany(TblJabatan::className(), ['krj' => 'kd_kerja']);
    }
}
