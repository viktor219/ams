<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "lv_invoice_shipments".
 *
 * @property integer $id
 * @property integer $shipmentid
 * @property integer $invoiceid
 * @property string $created_at
 */
class InvoiceShipments extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'lv_invoice_shipments';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['shipmentid', 'invoiceid'], 'required'],
            [['shipmentid', 'invoiceid'], 'integer'],
            [['created_at'], 'safe']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'shipmentid' => 'Shipmentid',
            'invoiceid' => 'Invoiceid',
            'created_at' => 'Created At',
        ];
    }
}
