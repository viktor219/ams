<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "lv_user_log_location_tracking".
 *
 * @property integer $id
 * @property string $continent_code
 * @property string $contry_code
 * @property string $country_code_3
 * @property string $country_name
 * @property string $region
 * @property string $region_name
 * @property string $city
 * @property string $postal_code
 * @property string $latitude
 * @property string $longitude
 * @property string $timezone
 * @property string $created_at
 */
class UserLogLocationTracking extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'lv_user_log_location_tracking';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['continent_code', 'contry_code', 'country_code_3', 'region', 'country_name', 'region_name', 'city', 'latitude', 'longitude', 'postal_code', 'area_code', 'dma_code', 'currency_code', 'currency_symbol', 'timezone', 'created_at'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'continent_code' => 'Continent Code',
            'contry_code' => 'Contry Code',
            'country_code_3' => 'Country Code 3',
            'country_name' => 'Country Name',
            'region' => 'Region',
            'region_name' => 'Region Name',
            'city' => 'City',
            'postal_code' => 'Postal Code',
            'latitude' => 'Latitude',
            'longitude' => 'Longitude',
            'timezone' => 'Timezone',
            'created_at' => 'Created At',
        ];
    }
}
