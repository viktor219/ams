<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "{{%purchases}}".
 *
 * @property integer $id
 * @property integer $customer_id
 * @property integer $vendor_id
 * @property integer $shipping_company
 * @property integer $location_id
 * @property string $number_generated
 * @property string $number_radius
 * @property string $number_bb
 * @property string $notes
 * @property integer $status
 * @property integer $ordertype
 * @property integer $orderfile
 * @property integer $type
 * @property integer $returned
 * @property string $returneddate
 * @property string $estimated_time
 * @property string $trackingnumber
 * @property integer $trackinglink
 * @property string $dateshipped
 * @property string $shipby
 * @property integer $requestedby
 * @property string $trucknum
 * @property string $sealnum
 * @property string $dateonsite
 * @property string $deleted
 * @property string $created_at
 * @property string $modified_at
 */
class Purchase extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%purchases}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id', 'vendor_id', 'shipping_company', 'status', 'type', 'requestedby', 'estimated_time', 'number_generated', 'salesordernumber', 'trackingnumber', 'deleted' ,'created_at', 'modified_at'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'customer_id' => 'Customer ID',
            'vendor_id' => 'Vendor',
            'shipping_company' => 'Shipping Company',
            'number_generated' => 'PO#',
            'status' => 'Status',
            'type' => 'Type',
            'estimated_time' => 'Estimated Time',
            'trackingnumber' => 'Tracking#',
            'requestedby' => 'Requested By',
            'dateonsite' => 'Dateonsite',
            'deleted' => 'Deleted',
            'created_at' => 'Created At',
            'modified_at' => 'Modified At',
        ];
    }
}