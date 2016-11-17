<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "{{%user_mail_accounts}}".
 *
 * @property integer $userid
 * @property string $password
 */
class UserMailAccount extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%user_mail_accounts}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['password'], 'required'],
            [['password'], 'string', 'max' => 100]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'userid' => 'Userid',
            'password' => 'Password',
        ];
    }
}
