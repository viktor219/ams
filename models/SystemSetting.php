<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "{{%system_setting}}".
 *
 * @property string $account_number
 * @property string $shipping_method
 */
class SystemSetting extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%system_setting}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['account_number', 'shipping_method'], 'string', 'max' => 100],
            [['sales_taxerate'], 'integer']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'account_number' => 'Account Number',
            'shipping_method' => 'Shipping Method',
        ];
    }
}
