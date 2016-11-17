<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "{{%returnedlabels_mails}}".
 *
 * @property integer $id
 * @property integer $orderid
 * @property string $email
 * @property string $date_sent
 */
class ReturnedlabelMail extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%returnedlabels_mails}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['orderid', 'email'], 'required'],
            [['orderid'], 'integer'],
            [['date_sent'], 'safe'],
            [['email'], 'string', 'max' => 100]
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
            'email' => 'Email',
            'date_sent' => 'Date Sent',
        ];
    }
}
