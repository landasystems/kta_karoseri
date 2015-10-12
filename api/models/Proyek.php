<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "m_user".
 *
 * @property integer $id
 * @property integer $roles_id
 * @property string $nama
 * @property string $username
 * @property string $password
 * @property integer $is_deleted
 * @property string $created_at
 * @property integer $created_by
 * @property string $modified_at
 * @property integer $modified_by
 */
class Proyek extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'proyek';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['kode', 'nama_proyek'], 'safe'],
            [['kode', 'nama_proyek'], 'string', 'max' => 255]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'kode' => 'Kode',
            'nama_proyek' => 'Nama Proyek',
        ];
    }
    
}
