<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "trans_bbk".
 *
 * @property string $no_bbk
 * @property string $no_wo
 * @property string $tanggal
 * @property string $penerima
 * @property string $kd_jab
 * @property string $petugas
 * @property integer $status
 */
class TransBbk extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'trans_bbk';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['no_bbk'], 'required'],
            [['tanggal'], 'safe'],
            [['status'], 'integer'],
            [['no_bbk', 'no_wo'], 'string', 'max' => 10],
            [['penerima', 'kd_jab'], 'string', 'max' => 20],
            [['petugas'], 'string', 'max' => 50],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'no_bbk' => 'No Bbk',
            'no_wo' => 'No Wo',
            'tanggal' => 'Tanggal',
            'penerima' => 'Penerima',
            'kd_jab' => 'Kd Jab',
            'petugas' => 'Petugas',
            'status' => 'Status',
        ];
    }
}
