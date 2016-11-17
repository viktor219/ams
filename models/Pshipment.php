<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "{{%pshipments}}".
 *
 * @property integer $id
 * @property integer $purchaseid
 * @property integer $shipping_deliverymethod
 * @property integer $accountnumber
 * @property string $created_at
 * @property string $modified_at
 */
class Pshipment extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%pshipments}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['purchaseid', 'shipping_deliverymethod', 'accountnumber'], 'required'],
            [['purchaseid', 'shipping_deliverymethod', 'accountnumber'], 'integer'],
            [['created_at', 'modified_at'], 'safe']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'purchaseid' => 'Purchaseid',
            'shipping_deliverymethod' => 'Shipping Deliverymethod',
            'accountnumber' => 'Accountnumber',
            'created_at' => 'Created At',
            'modified_at' => 'Modified At',
        ];
    }
}
