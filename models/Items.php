<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "{{%items}}".
 *
 * @property integer $id
 * @property integer $owner_id
 * @property string $tagnum
 * @property integer $package_optionid
 * @property integer $conditionid
 * @property integer $status
 * @property string $serial
 * @property string $lane
 * @property string $model
 * @property integer $customer
 * @property integer $location
 * @property string $picked
 * @property integer $ordernumber
 * @property integer $purchaseordernumber
 * @property string $received
 * @property integer $receiving_pallet
 * @property string $shipped
 * @property integer $labelprinted
 * @property string $trackingnumber
 * @property integer $trackinglink
 * @property string $returned
 * @property string $lastupdated
 * @property integer $requested
 * @property integer $prioritysort
 * @property string $requestedlocation
 * @property integer $shippingpallet
 * @property string $terminalnum
 * @property integer $incomingpalletnumber
 * @property integer $incomingboxnumber
 * @property integer $outgoingpalletnumber
 * @property integer $outgoingboxnumber
 * @property string $notes
 * @property string $transferred
 * @property integer $deleted
 * @property string $created_at
 * @property string $modified_at
 */
class Items extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%items}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['owner_id', 'package_optionid', 'conditionid', 'status', 'customer', 'location', 'ordernumber', 'purchaseordernumber', 'receiving_pallet', 'labelprinted', 'trackinglink', 'requested', 'prioritysort', 'shippingpallet', 'incomingpalletnumber', 'incomingboxnumber', 'outgoingpalletnumber', 'outgoingboxnumber', 'deleted'], 'integer'],
            [['picked', 'received', 'shipped', 'returned', 'lastupdated', 'transferred', 'created_at', 'modified_at'], 'safe'],
            [['notes'], 'string'],
            [['tagnum', 'serial', 'model', 'trackingnumber', 'requestedlocation'], 'string', 'max' => 100],
            [['lane', 'terminalnum'], 'string', 'max' => 40]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'owner_id' => 'Owner ID',
            'tagnum' => 'Tagnum',
            'package_optionid' => 'Package Optionid',
            'conditionid' => 'Conditionid',
            'status' => 'Status',
            'serial' => 'Serial',
            'lane' => 'Lane',
            'model' => 'Model',
            'customer' => 'Customer',
            'location' => 'Location',
            'picked' => 'Picked',
            'ordernumber' => 'Ordernumber',
            'purchaseordernumber' => 'Purchaseordernumber',
            'received' => 'Received',
            'receiving_pallet' => 'Receiving Pallet',
            'shipped' => 'Shipped',
            'labelprinted' => 'Labelprinted',
            'trackingnumber' => 'Trackingnumber',
            'trackinglink' => 'Trackinglink',
            'returned' => 'Returned',
            'lastupdated' => 'Lastupdated',
            'requested' => 'Requested',
            'prioritysort' => 'Prioritysort',
            'requestedlocation' => 'Requestedlocation',
            'shippingpallet' => 'Shippingpallet',
            'terminalnum' => 'Terminalnum',
            'incomingpalletnumber' => 'Incomingpalletnumber',
            'incomingboxnumber' => 'Incomingboxnumber',
            'outgoingpalletnumber' => 'Outgoingpalletnumber',
            'outgoingboxnumber' => 'Outgoingboxnumber',
            'notes' => 'Notes',
            'transferred' => 'Transferred',
            'deleted' => 'Deleted',
            'created_at' => 'Created At',
            'modified_at' => 'Modified At',
        ];
    }
}
