<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "missing_locations".
 *
 * @property integer $id
 * @property string $division_id
 * @property string $storenum
 * @property string $storename
 * @property string $address
 * @property string $address2
 * @property string $city
 * @property string $state
 * @property string $zipcode
 * @property string $phone
 */
class MissingLocation extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'missing_locations';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['division_id'], 'string', 'max' => 4],
            [['storenum', 'address2'], 'string', 'max' => 10],
            [['storename'], 'string', 'max' => 29],
            [['address'], 'string', 'max' => 24],
            [['city'], 'string', 'max' => 14],
            [['state'], 'string', 'max' => 2],
            [['zipcode'], 'string', 'max' => 5],
            [['phone'], 'string', 'max' => 12]
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
            'address2' => 'Address2',
            'city' => 'City',
            'state' => 'State',
            'zipcode' => 'Zipcode',
            'phone' => 'Phone',
        ];
    }
}
