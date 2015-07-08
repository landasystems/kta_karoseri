<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "m_roles".
 *
 * @property integer $id
 * @property string $nama
 * @property integer $is_deleted
 */
class Roles extends \yii\db\ActiveRecord {

    /**
     * @inheritdoc
     */
    public static function tableName() {
        return 'm_roles';
    }

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['nama'], 'unique'],
            [['akses'], 'safe'],
            [['is_deleted'], 'integer'],
            [['nama'], 'string', 'max' => 45]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'id' => 'ID',
            'nama' => 'Nama',
            'akses' => 'Akses',
            'is_deleted' => 'Is Deleted',
        ];
    }

}
