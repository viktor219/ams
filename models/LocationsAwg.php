<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "locations_awg".
 *
 * @property integer $id
 * @property string $company
 * @property string $storename
 * @property string $storenum
 * @property string $address
 * @property string $address2
 * @property string $city
 * @property string $state
 * @property string $zipcode
 * @property string $phone
 * @property string $email
 */
class LocationsAwg extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'locations_awg';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['company', 'storename', 'storenum', 'address', 'address2', 'city', 'phone', 'email'], 'string', 'max' => 100],
            [['state', 'zipcode'], 'string', 'max' => 20]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'company' => 'Company',
            'storename' => 'Storename',
            'storenum' => 'Storenum',
            'address' => 'Address',
            'address2' => 'Address2',
            'city' => 'City',
            'state' => 'State',
            'zipcode' => 'Zipcode',
            'phone' => 'Phone',
            'email' => 'Email',
        ];
    }
}
