<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "small_int".
 *
 * @property string $no_wo
 * @property string $plavon
 * @property string $bagasi_dalam
 * @property string $duchting_louver
 * @property string $trimming_deck
 * @property string $lampu_plavon
 * @property string $dashboard
 * @property string $lantai
 * @property string $karpet
 * @property string $peredam
 * @property string $pegangan_tangan_atas
 * @property string $pengaman_penumpang
 * @property string $pengaman_kaca_samping
 * @property string $pengaman_driver
 * @property string $gordyn
 * @property string $driver_fan
 * @property string $radio_tape
 * @property string $spek_seat
 * @property string $driver_seat
 * @property string $cover_seat
 * @property string $optional_seat
 * @property string $total_seat
 * @property string $merk_ac
 * @property string $lain2
 */
class Smallint extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'small_int';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['lain2'], 'string'],
            [['no_wo'], 'string', 'max' => 20],
            [['plavon', 'bagasi_dalam', 'duchting_louver', 'trimming_deck', 'lampu_plavon', 'dashboard', 'lantai', 'karpet', 'peredam', 'pegangan_tangan_atas', 'pengaman_penumpang', 'pengaman_kaca_samping', 'pengaman_driver', 'gordyn', 'driver_fan', 'radio_tape', 'spek_seat', 'driver_seat', 'cover_seat', 'optional_seat', 'merk_ac'], 'string', 'max' => 100],
            [['total_seat'], 'string', 'max' => 1000]
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
            'bagasi_dalam' => 'Bagasi Dalam',
            'duchting_louver' => 'Duchting Louver',
            'trimming_deck' => 'Trimming Deck',
            'lampu_plavon' => 'Lampu Plavon',
            'dashboard' => 'Dashboard',
            'lantai' => 'Lantai',
            'karpet' => 'Karpet',
            'peredam' => 'Peredam',
            'pegangan_tangan_atas' => 'Pegangan Tangan Atas',
            'pengaman_penumpang' => 'Pengaman Penumpang',
            'pengaman_kaca_samping' => 'Pengaman Kaca Samping',
            'pengaman_driver' => 'Pengaman Driver',
            'gordyn' => 'Gordyn',
            'driver_fan' => 'Driver Fan',
            'radio_tape' => 'Radio Tape',
            'spek_seat' => 'Spek Seat',
            'driver_seat' => 'Driver Seat',
            'cover_seat' => 'Cover Seat',
            'optional_seat' => 'Optional Seat',
            'total_seat' => 'Total Seat',
            'merk_ac' => 'Merk Ac',
            'lain2' => 'Lain2',
        ];
    }
}
