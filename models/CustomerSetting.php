<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "{{%customer_setting}}".
 *
 * @property integer $customerid
 * @property string $default_account_number
 * @property string $default_shipping_method
 * @property string $secondary_account_number
 * @property string $secondary_shipping_method
 */
class CustomerSetting extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%customer_setting}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['customerid', 'default_account_number', 'default_shipping_method'], 'required'],
            [['customerid'], 'integer'],
            [['default_account_number', 'default_shipping_method', 'secondary_account_number', 'secondary_shipping_method'], 'string', 'max' => 100]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'customerid' => 'Customerid',
            'default_account_number' => 'Default Account Number',
            'default_shipping_method' => 'Default Shipping Method',
            'secondary_account_number' => 'Secondary Account Number',
            'secondary_shipping_method' => 'Secondary Shipping Method',
        ];
    }
}
