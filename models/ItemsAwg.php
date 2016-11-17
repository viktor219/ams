<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "items_awg".
 *
 * @property integer $id
 * @property string $status
 * @property string $serial
 * @property string $model
 * @property string $customer
 * @property string $location
 * @property string $picked
 * @property string $shipmentnumber
 * @property string $received
 * @property integer $receiving_pallet
 * @property string $shipped
 * @property string $trackingnumber
 * @property integer $trackinglink
 * @property string $returned
 * @property string $lastupdated
 * @property integer $requested
 * @property integer $prioritysort
 * @property string $requestedlocation
 * @property integer $shippingpallet
 * @property string $terminalnum
 * @property string $notes
 * @property string $transferred
 */
class ItemsAwg extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'items_awg';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['picked', 'received', 'shipped', 'returned', 'lastupdated', 'transferred'], 'safe'],
            [['receiving_pallet', 'trackinglink', 'requested', 'prioritysort', 'shippingpallet'], 'integer'],
            [['status', 'serial', 'model', 'customer', 'location', 'trackingnumber', 'requestedlocation'], 'string', 'max' => 100],
            [['shipmentnumber'], 'string', 'max' => 255],
            [['terminalnum'], 'string', 'max' => 40],
            [['notes'], 'string', 'max' => 50]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'status' => 'Status',
            'serial' => 'Serial',
            'model' => 'Model',
            'customer' => 'Customer',
            'location' => 'Location',
            'picked' => 'Picked',
            'shipmentnumber' => 'Shipmentnumber',
            'received' => 'Received',
            'receiving_pallet' => 'Receiving Pallet',
            'shipped' => 'Shipped',
            'trackingnumber' => 'Trackingnumber',
            'trackinglink' => 'Trackinglink',
            'returned' => 'Returned',
            'lastupdated' => 'Lastupdated',
            'requested' => 'Requested',
            'prioritysort' => 'Prioritysort',
            'requestedlocation' => 'Requestedlocation',
            'shippingpallet' => 'Shippingpallet',
            'terminalnum' => 'Terminalnum',
            'notes' => 'Notes',
            'transferred' => 'Transferred',
        ];
    }
}
