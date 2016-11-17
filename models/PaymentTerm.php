<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "{{%customer_payment_terms}}".
 *
 * @property integer $id
 * @property string $code
 * @property string $description
 */
class PaymentTerm extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%customer_payment_terms}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['code', 'description'], 'required'],
            [['description'], 'string'],
            [['code'], 'string', 'max' => 25]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'code' => 'Code',
            'description' => 'Description',
        ];
    }
}
