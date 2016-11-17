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
 * @property integer $outgoingboxnumber
 * @property integer $outgoingpalletnumber
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
		13 => 'Delivered',
		14 => 'Arrived',
		15 => 'Ready to Invoice',
		16 => 'Invoiced',
		17 => 'Complete',
		18 => 'Transferred',
		19 => 'Requested for Service',
		20 => 'Used for Service',
		21 => 'Breakdown',
		22 => 'Scrap',
		23 => 'Awaiting Return'
	);
	
	/*public static $status = array(
		'1' => 'In Stock',
		'2' => 'In Progress',
		'2.5' => 'Ready to =  ship',
		'3' => 'shipped'
	);*/
	
	public static $readystatus = array(
		10 => 'In Shipping',
		11 => 'Ready to ship',
		12 => 'Shipped',
		13 => 'Arrived',
		14 => 'Ready to Invoice',
		15 => 'Invoiced',
		16 => 'Complete'	
	);
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
		9 => 'Serviced',
		18 => 'Requested for Service',
		19 => 'Used for Service'
	);
	
	public static $testingstatus = array(
		18 => 'Requested for Service',
		19 => 'Used for Service'
	);
	
	public static $shippingstatus = array(
		//9 => 'Ready to ship'
		10 => 'In Shipping'
	);

	public static $shippingallstatus = array(
		10 => 'In Shipping',
		11 => 'Ready to ship',
		12 => 'Shipped'
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
            [['owner_id', 'picked', 'package_optionid', 'conditionid', 'received', 'shipped', 'returned', 'labelprinted', 'lastupdated', 'receiving_pallet', 'trackinglink', 'requested', 'prioritysort', 'shippingpallet', 'status', 'serial', 'lane', 'model', 'customer', 'location', 'outgoingpalletnumber', 'outgoingboxnumber','trackingnumber', 'requestedlocation', 'ordernumber', 'terminalnum', 'incomingpalletnumber', 'incomingboxnumber', 'deleted','created_at', 'modified_at'], 'safe'],
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
            'outgoingpalletnumber' => 'Outgoing Box Number', 
            'outgoingboxnumber' => 'Outgoing Pallet Number',
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
    
    public function afterSave($insert, $changedAttributes){ 
        parent::afterSave($insert, $changedAttributes);
        $recentActivity = New Recentactivity;
        $recentActivity->pk = $this->id;
        $recentActivity->customer_id = $this->customer;
        $recentActivity->user_id = Yii::$app->user->id;
        $recentActivity->created_at = date('Y-m-d H:i:s');
        $itemsCount = Item::find()->where(['status' => $this->status, 'model' => $this->model,'customer' => $this->customer])->count();
        if($this->status == array_search('In Progress', self::$status)){
            $recentActivity->type = array_search('Items In Progress', Recentactivity::$type);
        } elseif($this->status == array_search('Shipped', self::$status)){
            $recentActivity->type = array_search('Items Shipped', Recentactivity::$type);
        }
        
        if($this->status == array_search('In Stock', self::$status)){
            $db = \Yii::$app->common->getMongoDb();
            $collection = $db->inventorymodels;
            $inStockCount = self::find()->where(['status' => array_search('In Stock', self::$status), 'model' => $this->model])->count();
            $changedAttributes['instock_qty'] = (string) $inStockCount;
            $collection->update(array('id' => (string)  $this->model), array('$set' => $changedAttributes));
        }
        $recentActivity->itemscount = $itemsCount;
        if($insert && !empty($recentActivity->type)){
            $recentActivity->is_new = 1;
            $recentActivity->save(false);
        } elseif(count($changedAttributes) && !empty($recentActivity->type)){
            $recentActivity->is_new = 0;
            $recentActivity->save(false);
        }
    }
}
