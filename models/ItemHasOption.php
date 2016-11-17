<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "{{%item_has_option}}".
 *
 * @property integer $id
 * @property integer $itemid
 * @property integer $optionid
 * @property integer $ordertype
 * @property string $created_at
 */
class ItemHasOption extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%item_has_option}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
        	[['orderid'], 'required'],
            [['itemid', 'orderid', 'optionid', 'purchaseid', 'ordertype', 'created_at'], 'safe']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'itemid' => 'Itemid',
            'optionid' => 'Optionid',
            'ordertype' => 'Ordertype',
            'created_at' => 'Created At',
        ];
    }
}
