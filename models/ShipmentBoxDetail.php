<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "{{%shipments_box_details}}".
 *
 * @property integer $id
 * @property integer $shipmentid
 * @property integer $modelid
 * @property integer $pallet_box_number
 * @property string $weight
 * @property string $height
 * @property string $length
 * @property string $depth
 * @property string $label_image
 * @property string $trackingnumber
 * @property string $label_html
 * @property string $created_at
 * @property string $modified_at
 */
class ShipmentBoxDetail extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%shipments_box_details}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['shipmentid', 'modelid'], 'required'],
            [['shipmentid', 'modelid', 'pallet_box_number'], 'integer'],
            [['weight', 'height', 'length', 'depth'], 'number'],
            [['label_image', 'trackingnumber', 'label_html', 'created_at', 'modified_at'], 'safe']
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
            'modelid' => 'Modelid',
            'pallet_box_number' => 'Pallet Box Number',
            'weight' => 'Weight',
            'height' => 'Height',
            'length' => 'Length',
            'depth' => 'Depth',
            'label_image' => 'Label Image',
            'trackingnumber' => 'Trackingnumber',
            'label_html' => 'Label Html',
            'created_at' => 'Created At',
            'modified_at' => 'Modified At',
        ];
    }
}
