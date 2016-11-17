<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "{{%itemsorderedbuild}}".
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
class Itemsorderedbuild extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%itemsorderedbuild}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['ordernumber', 'package_optionid', 'customer', 'ordertype', 'status'], 'integer'],
            [['price'], 'number'],
            [['notes'], 'string'],
            [['timestamp'], 'safe'],
            [['qty', 'model'], 'string', 'max' => 11]
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
