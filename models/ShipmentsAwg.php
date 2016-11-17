<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "shipments_awg".
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
 * @property string $returntracking
 */
class ShipmentsAwg extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'shipments_awg';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['store', 'datecreated'], 'required'],
            [['store', 'type', 'returned'], 'integer'],
            [['returneddate', 'datecreated', 'dateshipped', 'shipby', 'dateonsite'], 'safe'],
            [['customer', 'trackinglink'], 'string', 'max' => 11],
            [['salesorder1', 'salesorder2', 'salesorder3', 'trackingnumber'], 'string', 'max' => 100],
            [['notes'], 'string', 'max' => 500],
            [['trucknum', 'sealnum'], 'string', 'max' => 55],
            [['returntracking'], 'string', 'max' => 255]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'customer' => 'Customer',
            'store' => 'Store',
            'salesorder1' => 'Salesorder1',
            'salesorder2' => 'Salesorder2',
            'salesorder3' => 'Salesorder3',
            'notes' => 'Notes',
            'type' => 'Type',
            'returned' => 'Returned',
            'returneddate' => 'Returneddate',
            'trackingnumber' => 'Trackingnumber',
            'trackinglink' => 'Trackinglink',
            'datecreated' => 'Datecreated',
            'dateshipped' => 'Dateshipped',
            'shipby' => 'Shipby',
            'trucknum' => 'Trucknum',
            'sealnum' => 'Sealnum',
            'dateonsite' => 'Dateonsite',
            'returntracking' => 'Returntracking',
        ];
    }
}
