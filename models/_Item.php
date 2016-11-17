<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "lv_items".
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
 * @property integer $deleted
 */
class Item extends \yii\db\ActiveRecord
{
	public static $status = array(
		1 => 'Requested', 
		2 => 'In Transit',
		3 => 'Received',
		4 => 'In Stock',
		5 => 'Reserved',
		6 => 'Picked',
		7 => 'In Progress',
		8 => 'Cleaned',
		9 => 'Serviced',
		10 => 'In Shipping',
		11 => 'Ready to ship',
		12 => 'Shipped',
		13 => 'Arrived',
		14 => 'Ready to Invoice',
		15 => 'Invoiced',
		16 => 'Complete',
		17 => 'Transferred',
		18 => 'Requested for Service',
		19 => 'Used for Service',
		20 => 'Breakdown',
		21 => 'Scrap'
	);
	
	/*public static $status = array(
		'1' => 'In Stock',
		'2' => 'In Progress',
		'2.5' => 'Ready to =  ship',
		'3' => 'shipped'
	);*/
	
	public static $inventorystatus = array(
		4 => 'In Stock',
		7 => 'In Progress',
		11 => 'Ready to ship'
	);
	
	/*public static $inventorystatus = array(
			'1' => 'In Stock',
			'2' => 'In Progress',
			'2.5' => 'Ready to ship'
	);*/
	
	public static $inprogressstatus = array(
		7 => 'In Progress',
		8 => 'Cleaned',
		18 => 'Requested for Service',
		19 => 'Used for Service'
	);
	
	public static $shippingstatus = array(
		//9 => 'Ready to ship'
		10 => 'In Shipping'
	);

	public static $shippingallstatus = array(
		10 => 'In Shipping',
		11 => 'Ready to ship'
	);
	
	public static $definedstatus = array(
		100 => 'Label Printing'
	);
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'lv_items';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
			[['notes'], 'string'],
            [['owner_id', 'picked', 'package_optionid', 'conditionid', 'received', 'shipped', 'returned', 'labelprinted', 'lastupdated', 'receiving_pallet', 'trackinglink', 'requested', 'prioritysort', 'shippingpallet', 'status', 'serial', 'lane', 'model', 'customer', 'location', 'trackingnumber', 'requestedlocation', 'ordernumber', 'terminalnum', 'incomingpalletnumber', 'incomingboxnumber', 'deleted','created_at', 'modified_at'], 'safe'],
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
            'deleted' => 'Deleted'
        ];
    }
}