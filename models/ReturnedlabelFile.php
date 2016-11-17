<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "{{%returnedlabel_files}}".
 *
 * @property integer $id
 * @property integer $orderid
 * @property string $filename
 * @property string $date_sent
 */
class ReturnedlabelFile extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%returnedlabel_files}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['orderid', 'filename'], 'required'],
            [['orderid'], 'integer'],
            [['date_sent'], 'safe'],
            [['filename'], 'string', 'max' => 100]
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
            'filename' => 'Filename',
            'date_sent' => 'Date Sent',
        ];
    }
}
