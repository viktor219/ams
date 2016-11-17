<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "{{%qsalesorders}}".
 *
 * @property integer $id
 * @property integer $customer_id
 * @property integer $location_id
 * @property string $number_generated
 * @property string $number_radius
 * @property string $number_bb
 * @property string $notes
 * @property integer $ordertype
 * @property integer $shippingcompany_id
 * @property integer $orderfile
 * @property integer $type
 * @property integer $returned
 * @property string $deleted
 * @property string $returneddate
 * @property string $trackingnumber
 * @property integer $trackinglink
 * @property string $dateshipped
 * @property string $shipby
 * @property string $trucknum
 * @property string $sealnum
 * @property string $dateonsite
 * @property string $created_at
 * @property string $modified_at
 */
class QOrder extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'lv_qsalesorders';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
       	 	[['notes'], 'string'],
            [['trucknum', 'sealnum', 'number_generated', 'customer_po', 'enduser_po', 'trackingnumber', 'customer_id', 'location_id', 'type', 'returned', 'trackinglink' ,'deleted', 'returneddate', 'ordertype', 'shippingcompany_id', 'orderfile', 'dateshipped', 'shipby', 'dateonsite', 'created_at', 'modified_at'], 'safe'],
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
            'customer_po' => 'Customer PO#',
            'enduser_po' => 'End user PO#',
            'notes' => 'Notes',
            'ordertype' => 'Ordertype',
            'shippingcompany_id' => 'Shippingcompany ID',
            'orderfile' => 'Orderfile',
            'type' => 'Type',
			'deleted' => 'Deleted',
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
