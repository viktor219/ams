<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "_l_locations".
 *
 * @property integer $id
 * @property string $division_id
 * @property string $storenum
 * @property string $storename
 * @property string $address
 * @property string $city
 * @property string $state
 * @property string $zipcode
 * @property string $notes
 */
class Gimplocation extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '_l_locations';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['division_id', 'zipcode'], 'string', 'max' => 10],
            [['storenum'], 'string', 'max' => 9],
            [['storename'], 'string', 'max' => 58],
            [['address'], 'string', 'max' => 45],
            [['city'], 'string', 'max' => 17],
            [['state'], 'string', 'max' => 2],
            [['notes'], 'string', 'max' => 34]
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
            'notes' => 'Notes',
        ];
    }
}
