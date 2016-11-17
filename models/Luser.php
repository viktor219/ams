<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "_l_users".
 *
 * @property integer $id
 * @property string $firstname
 * @property string $lastname
 * @property string $username
 * @property string $email
 * @property string $division
 * @property string $access
 */
class Luser extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '_l_users';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['firstname'], 'string', 'max' => 8],
            [['lastname'], 'string', 'max' => 10],
            [['username'], 'string', 'max' => 11],
            [['email'], 'string', 'max' => 27],
            [['division'], 'string', 'max' => 4],
            [['access'], 'string', 'max' => 14]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'firstname' => 'Firstname',
            'lastname' => 'Lastname',
            'username' => 'Username',
            'email' => 'Email',
            'division' => 'Division',
            'access' => 'Access',
        ];
    }
}
