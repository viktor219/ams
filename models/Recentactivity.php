<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "lv_recentactivity".
 *
 * @property integer $id
 * @property integer $type
 * @property integer $pk
 * @property integer $is_new
 * @property integer $customer_id
 * @property integer $user_id
 * @property integer $usertype
 * @property integer $itemscount
 * @property string $created_at
 */
class Recentactivity extends \yii\db\ActiveRecord
{
    public static $type = array(
		1 => 'User', 
		2 => 'Model',
		3 => 'Items In Progress',
                4 => 'Items Received',
		5 => 'Items Shipped',
		6 => 'Shipment',
		7 => 'Order',
	);
    
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'lv_recentactivity';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['type', 'pk', 'user_id', 'created_at'], 'required'],
            [['type', 'pk', 'is_new', 'customer_id', 'user_id', 'usertype', 'itemscount'], 'integer'],
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
            'type' => 'Type',
            'pk' => 'Pk',
            'is_new' => 'Is New',
            'customer_id' => 'Customer ID',
            'user_id' => 'User ID',
            'usertype' => 'Usertype',
            'itemscount' => 'Itemscount',
            'created_at' => 'Created At',
        ];
    }
}
