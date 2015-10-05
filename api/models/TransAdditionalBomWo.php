<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "trans_additional_bom_wo".
 *
 * @property integer $id
 * @property integer $tran_additional_bom_id
 * @property string $no_wo
 */
class TransAdditionalBomWo extends \yii\db\ActiveRecord {

    /**
     * @inheritdoc
     */
    public static function tableName() {
        return 'trans_additional_bom_wo';
    }

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['tran_additional_bom_id', 'no_wo'], 'required'],
            [['tran_additional_bom_id'], 'integer'],
            [['no_wo'], 'string', 'max' => 30]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'id' => 'ID',
            'tran_additional_bom_id' => 'Tran Additional Bom ID',
            'no_wo' => 'No Wo',
        ];
    }

    public function getTransadditionalbom() {
        return $this->hasOne(TransAdditionalBom::className(), ['id' => 'tran_additional_bom_id']);
    }

}
