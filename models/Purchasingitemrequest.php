<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "{{%purchasingitemrequest}}".
 *
 * @property integer $id
 * @property integer $requestby
 * @property integer $qty
 * @property integer $item
 * @property integer $ordernumber
 * @property string $created_at
 * @property string $modified_at
 */
class Purchasingitemrequest extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%purchasingitemrequest}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['requestby', 'qty', 'item', 'ordernumber'], 'required'],
            [['requestby', 'qty', 'item', 'ordernumber'], 'integer'],
            [['created_at', 'modified_at'], 'safe']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'requestby' => 'Requestby',
            'qty' => 'Qty',
            'item' => 'Item',
            'ordernumber' => 'Ordernumber',
            'created_at' => 'Created At',
            'modified_at' => 'Modified At',
        ];
    }
}
