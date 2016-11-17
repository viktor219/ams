<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "{{%qitem_has_option}}".
 *
 * @property integer $id
 * @property integer $itemid
 * @property integer $orderid
 * @property integer $purchaseid
 * @property integer $optionid
 * @property integer $ordertype
 * @property string $created_at
 */
class QItemHasOption extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%qitem_has_option}}';
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
            'orderid' => 'Orderid',
            'purchaseid' => 'Purchaseid',
            'optionid' => 'Optionid',
            'ordertype' => 'Ordertype',
            'created_at' => 'Created At',
        ];
    }
}
