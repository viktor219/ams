<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "lv_shipments_items".
 *
 * @property integer $id
 * @property integer $shipmentid
 * @property integer $itemid
 * @property string $date_added
 */
class ShipmentsItems extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'lv_shipments_items';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['shipmentid', 'itemid'], 'integer'],
            [['date_added'], 'safe']
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
            'itemid' => 'Itemid',
            'date_added' => 'Date Added',
        ];
    }
}
