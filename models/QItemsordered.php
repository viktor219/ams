<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "{{%qitemsordered}}".
 *
 * @property integer $id
 * @property integer $ordernumber
 * @property integer $package_optionid
 * @property integer $customer
 * @property integer $ordertype
 * @property string $qty
 * @property string $model
 * @property string $price
 * @property integer $status
 * @property string $notes
 * @property string $timestamp
 */
class QItemsordered extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%qitemsordered}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
        	[['price'], 'number'],
        	[['notes'], 'string'],
            [['customer', 'ordernumber', 'package_optionid', 'ordertype', 'status', 'qty', 'model', 'timestamp'], 'safe'],
        ];
    }
    

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'ordernumber' => 'Ordernumber',
            'package_optionid' => 'Package Optionid',
            'customer' => 'Customer',
            'ordertype' => 'Ordertype',
            'qty' => 'Qty',
            'model' => 'Model',
            'price' => 'Price',
            'status' => 'Status',
            'notes' => 'Notes',
            'timestamp' => 'Timestamp',
        ];
    }
}
