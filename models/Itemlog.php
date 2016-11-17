<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "{{%itemslog}}".
 *
 * @property integer $id
 * @property integer $itemid
 * @property integer $status
 * @property integer $userid
 * @property string $created_at
 */
class Itemlog extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%itemslog}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['itemid', 'locationid', 'status', 'userid', 'incomingstorenumber', 'shipment_id', 'created_at'], 'safe']
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
            'status' => 'Status',
            'userid' => 'Userid',
            'created_at' => 'Logged At',
        ];
    }
}
