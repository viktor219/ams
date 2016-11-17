<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "{{%receive}}".
 *
 * @property integer $id
 * @property integer $ordernumber
 * @property integer $item_id
 * @property integer $qty
 */
class Receive extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%receive}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            //[['ordernumber', 'item_id', 'qty'], 'required'],
            [['ordernumber', 'item_id', 'qty'], 'integer']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'ordernumber' => 'Ordernumber',
            'item_id' => 'Item ID',
            'qty' => 'Qty',
        ];
    }
}
