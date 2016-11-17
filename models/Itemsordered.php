<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "lv_itemsordered".
 *
 * @property integer $id
 * @property string $qty
 * @property string $model
 * @property integer $ordernumber
 * @property string $timestamp
 * @property integer $status
 */
class Itemsordered extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'lv_itemsordered';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
        	[['notes'], 'string'],
            [['customer', 'ordernumber', 'price', 'package_optionid', 'ordertype', 'status', 'qty', 'model', 'timestamp'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'qty' => 'Qty',
            'model' => 'Model',
            'ordernumber' => 'Ordernumber',
            'timestamp' => 'Timestamp',
            'status' => 'Status',
        ];
    }
}
