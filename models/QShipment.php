<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "{{%qshipments}}".
 *
 * @property integer $id
 * @property integer $orderid
 * @property string $accountnumber
 * @property integer $shipping_deliverymethod
 * @property integer $locationid
 * @property string $trackingnumber
 * @property integer $trackinglink
 * @property string $dateshipped
 */
class QShipment extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%qshipments}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['orderid', 'accountnumber', 'shipping_deliverymethod', 'locationid'], 'required'],
            [['orderid', 'shipping_deliverymethod', 'locationid', 'trackinglink'], 'integer'],
            [['dateshipped'], 'safe'],
            [['accountnumber', 'trackingnumber'], 'string', 'max' => 100]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'orderid' => 'Orderid',
            'accountnumber' => 'Accountnumber',
            'shipping_deliverymethod' => 'Shipping Deliverymethod',
            'locationid' => 'Locationid',
            'trackingnumber' => 'Trackingnumber',
            'trackinglink' => 'Trackinglink',
            'dateshipped' => 'Dateshipped',
        ];
    }
}
