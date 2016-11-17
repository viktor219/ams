<?php

namespace app\modules\orders\models;

use Yii;

/**
 * This is the model class for table "shipments".
 *
 * @property integer $id
 * @property string $customer
 * @property integer $store
 * @property string $salesorder1
 * @property string $salesorder2
 * @property string $salesorder3
 * @property string $notes
 * @property integer $type
 * @property integer $returned
 * @property string $returneddate
 * @property string $trackingnumber
 * @property string $trackinglink
 * @property string $datecreated
 * @property string $dateshipped
 * @property string $shipby
 * @property string $trucknum
 * @property string $sealnum
 * @property string $dateonsite
 */
class Order extends \yii\db\ActiveRecord
{
	public $location;
	public $customButtons;
	public $status;
     /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%salesorders}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
       	 	[['notes'], 'string'],
            [['trucknum', 'sealnum', 'number_generated', 'customer_po', 'enduser_po', 'trackingnumber', 'customer_id', 'location_id', 'type', 'returned', 'trackinglink', 'returneddate', 'ordertype', 'shippingcompany_id', 'orderfile', 'dateshipped', 'shipby', 'dateonsite', 'created_at', 'modified_at', 'deleted'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'customer_id' => 'Customer ID',
            'location_id' => 'Location ID',
            'number_generated' => 'Number Generated',
            'customer_po' => 'Number Radius',
            'enduser_po' => 'Number Bb',
            'notes' => 'Notes',
            'type' => 'Type',
            'returned' => 'Returned',
            'returneddate' => 'Returneddate',
            'trackingnumber' => 'Trackingnumber',
            'trackinglink' => 'Trackinglink',
            'dateshipped' => 'Dateshipped',
            'shipby' => 'Shipby',
            'trucknum' => 'Trucknum',
            'sealnum' => 'Sealnum',
            'dateonsite' => 'Dateonsite',
            'created_at' => 'Created At',
            'modified_at' => 'Modified At',
        ];
    }
}
