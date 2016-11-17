<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "{{%shipments}}".
 *
 * @property integer $id
 * @property integer $orderid
 * @property string $accountnumber
 * @property integer $shipping_deliverymethod
 * @property integer $locationid
 * @property string $master_trackingnumber
 * @property integer $trackinglink
 * @property float $shipping_cost
 * @property string $dateshipped
 */
class Shipment extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%shipments}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['trackinglink'], 'integer'],
            [['orderid', 'accountnumber', 'shipping_deliverymethod', 'shipping_cost', 'locationid', 'dateshipped'], 'safe'],
            [['accountnumber', 'master_trackingnumber'], 'string', 'max' => 100]
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
            'master_trackingnumber' => 'Master Trackingnumber',
            'trackinglink' => 'Trackinglink',
            'shipping_cost' => 'Shipping Cost',
            'dateshipped' => 'Dateshipped',
        ];
    }
}
