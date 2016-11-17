<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "lv_locations_details".
 *
 * @property integer $id
 * @property integer $locationid
 * @property string $ipaddress
 * @property string $subnet_mask
 * @property string $gateway
 * @property string $primary_dns
 * @property string $secondary_dns
 * @property string $wins_server
 * @property string $created_at
 */
class LocationDetails extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'lv_locations_details';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['locationid'], 'integer'],
            [['require_dialout', 'created_at'], 'safe'],
            [['ipaddress', 'subnet_mask', 'gateway', 'primary_dns', 'secondary_dns', 'wins_server'], 'string', 'max' => 255]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'locationid' => 'Locationid',
            'ipaddress' => 'Ipaddress',
            'subnet_mask' => 'Subnet Mask',
            'gateway' => 'Gateway',
            'primary_dns' => 'Primary Dns',
            'secondary_dns' => 'Secondary Dns',
            'wins_server' => 'Wins Server',
            'created_at' => 'Created At',
        ];
    }
}
