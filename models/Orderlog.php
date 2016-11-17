<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "{{%orderlog}}".
 *
 * @property integer $id
 * @property integer $orderid
 * @property integer $userid
 * @property integer $status
 * @property string $created_at
 * @property string $modified_at
 */
class Orderlog extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%orderlog}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['orderid', 'userid'], 'required'],
            [['orderid', 'userid', 'status'], 'integer'],
            [['created_at', 'modified_at'], 'safe'],
            [['ordertype'], 'default','value'=>1]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'orderid' => 'Orderid',
            'userid' => 'Userid',
            'status' => 'Status',
            'created_at' => 'Created At',
            'modified_at' => 'Modified At',
        ];
    }
}
