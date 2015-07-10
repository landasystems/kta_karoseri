<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "trans_claim".
 *
 * @property string $no_wo
 * @property string $jns_kend
 */
class TransClaim extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'trans_claim';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['no_wo', 'jns_kend'], 'string', 'max' => 10]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'no_wo' => 'No Wo',
            'jns_kend' => 'Jns Kend',
        ];
    }
}
