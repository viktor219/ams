<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "lv_locations_imported".
 *
 * @property integer $id
 * @property string $storenum
 * @property string $storename
 * @property string $address
 * @property string $city
 * @property string $state
 * @property string $zipcode
 */
class LocationImported extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'lv_locations_imported';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['storenum'], 'string', 'max' => 9],
            [['storename'], 'string', 'max' => 58],
            [['address'], 'string', 'max' => 45],
            [['city'], 'string', 'max' => 17],
            [['state'], 'string', 'max' => 2],
            [['zipcode'], 'string', 'max' => 10]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'storenum' => 'Storenum',
            'storename' => 'Storename',
            'address' => 'Address',
            'city' => 'City',
            'state' => 'State',
            'zipcode' => 'Zipcode',
        ];
    }
}
