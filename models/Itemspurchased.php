<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "{{%itemspurchased}}".
 *
 * @property integer $id
 * @property integer $package_optionid
 * @property integer $ordernumber
 * @property integer $model
 * @property integer $qty
 * @property string $price
 * @property integer $customer_id
 * @property integer $status
 * @property integer $ordertype
 * @property string $created_at
 * @property string $modified_at
 */
class Itemspurchased extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%itemspurchased}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['ordernumber', 'model', 'qty', 'price', 'status', 'created_at', 'modified_at'], 'safe']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'package_optionid' => 'Package Optionid',
            'ordernumber' => 'Ordernumber',
            'model' => 'Model',
            'qty' => 'Qty',
            'price' => 'Price',
            'customer_id' => 'Customer ID',
            'status' => 'Status',
            'ordertype' => 'Ordertype',
            'created_at' => 'Created At',
            'modified_at' => 'Modified At',
        ];
    }
}
