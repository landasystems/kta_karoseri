<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "mini_int".
 *
 * @property string $no_wo
 * @property string $plavon
 * @property string $trimming_deck
 * @property string $duchting_louver
 * @property string $lampu_plavon
 * @property string $lantai
 * @property string $karpet
 * @property string $konf_seat1
 * @property string $konf_seat2
 * @property string $konf_seat3
 * @property string $konf_seat4
 * @property string $konf_seat5
 * @property string $cover_seat
 * @property string $total_seat
 * @property string $lain2
 * @property string $merk_ac
 */
class Miniint extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'mini_int';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['lain2'], 'string'],
            [['no_wo'], 'string', 'max' => 20],
            [['plavon', 'trimming_deck', 'duchting_louver', 'lampu_plavon', 'lantai', 'karpet', 'konf_seat1', 'konf_seat2', 'konf_seat3', 'konf_seat4', 'konf_seat5', 'cover_seat', 'merk_ac'], 'string', 'max' => 100],
            [['total_seat'], 'string', 'max' => 500]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'no_wo' => 'No Wo',
            'plavon' => 'Plavon',
            'trimming_deck' => 'Trimming Deck',
            'duchting_louver' => 'Duchting Louver',
            'lampu_plavon' => 'Lampu Plavon',
            'lantai' => 'Lantai',
            'karpet' => 'Karpet',
            'konf_seat1' => 'Konf Seat1',
            'konf_seat2' => 'Konf Seat2',
            'konf_seat3' => 'Konf Seat3',
            'konf_seat4' => 'Konf Seat4',
            'konf_seat5' => 'Konf Seat5',
            'cover_seat' => 'Cover Seat',
            'total_seat' => 'Total Seat',
            'lain2' => 'Lain2',
            'merk_ac' => 'Merk Ac',
        ];
    }
}
