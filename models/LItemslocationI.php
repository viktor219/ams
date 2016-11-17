<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "_l_itemslocation_i".
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
class LItemslocationI extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '_l_itemslocation_i';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['site_id'], 'string', 'max' => 13],
            [['customer_name'], 'string', 'max' => 51],
            [['equipment_id', 'tagnumber'], 'string', 'max' => 8],
            [['equipment_description'], 'string', 'max' => 67],
            [['part_number'], 'string', 'max' => 20],
            [['model'], 'string', 'max' => 18],
            [['serialnumber'], 'string', 'max' => 15]
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
