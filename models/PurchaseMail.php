<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "{{%purchases_mails}}".
 *
 * @property integer $id
 * @property integer $purchaseid
 * @property string $email
 * @property string $date_sent
 */
class PurchaseMail extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%purchases_mails}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['purchaseid', 'email'], 'required'],
            [['purchaseid'], 'integer'],
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
            'purchaseid' => 'Purchaseid',
            'email' => 'Email',
            'date_sent' => 'Date Sent',
        ];
    }
}
