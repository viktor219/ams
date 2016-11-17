<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "_l_locationscontact".
 *
 * @property integer $id
 * @property string $division_id
 * @property string $storenum
 * @property string $storename
 * @property string $address
 * @property string $city
 * @property string $state
 * @property string $zipcode
 * @property string $phone
 * @property string $email
 */
class Llocationcontact extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '_l_locationscontact';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['division_id', 'state'], 'string', 'max' => 2],
            [['storenum'], 'string', 'max' => 7],
            [['storename'], 'string', 'max' => 41],
            [['address'], 'string', 'max' => 45],
            [['city'], 'string', 'max' => 17],
            [['zipcode'], 'string', 'max' => 10],
            [['phone'], 'string', 'max' => 12],
            [['email'], 'string', 'max' => 103]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'division_id' => 'Division ID',
            'storenum' => 'Storenum',
            'storename' => 'Storename',
            'address' => 'Address',
            'city' => 'City',
            'state' => 'State',
            'zipcode' => 'Zipcode',
            'phone' => 'Phone',
            'email' => 'Email',
        ];
    }
}
