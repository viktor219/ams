<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "_l_itemslocation".
 *
 * @property integer $id
 * @property string $site_id
 * @property string $customer_name
 * @property string $equipment_id
 * @property string $equipment_description
 * @property string $part_number
 * @property string $model
 * @property string $serialnumber
 * @property string $tagnumber
 */
class LItemlocation extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '_l_itemslocation';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['customer_name', 'equipment_id', 'tagnumber', 'equipment_description', 'part_number', 'model', 'serialnumber', 'site_id'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'site_id' => 'Site ID',
            'customer_name' => 'Customer Name',
            'equipment_id' => 'Equipment ID',
            'equipment_description' => 'Equipment Description',
            'part_number' => 'Part Number',
            'model' => 'Model',
            'serialnumber' => 'Serialnumber',
            'tagnumber' => 'Tagnumber',
        ];
    }
}
