<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "{{%wccart}}".
 *
 * @property integer $id
 * @property integer $userid
 * @property integer $modelid
 * @property string $created_at
 */
class Wccart extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%wccart}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['userid', 'modelid'], 'required'],
            [['userid', 'modelid'], 'integer'],
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
            'userid' => 'Userid',
            'modelid' => 'Modelid',
            'created_at' => 'Created At',
        ];
    }
}
