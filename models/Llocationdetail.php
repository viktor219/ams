<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "_l_locationsdetails".
 *
 * @property integer $id
 * @property integer $storenum
 * @property string $connection_type
 * @property string $ipaddress
 * @property string $subnet_mask
 * @property string $gateway
 * @property string $primary_dns
 * @property string $secondary_dns
 * @property string $wins_server
 * @property string $notes
 */
class Llocationdetail extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '_l_locationsdetails';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['storenum'], 'integer'],
            [['connection_type'], 'string', 'max' => 9],
            [['ipaddress', 'subnet_mask', 'gateway', 'primary_dns', 'secondary_dns', 'wins_server'], 'string', 'max' => 15],
            [['notes'], 'string', 'max' => 56]
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
            'connection_type' => 'Connection Type',
            'ipaddress' => 'Ipaddress',
            'subnet_mask' => 'Subnet Mask',
            'gateway' => 'Gateway',
            'primary_dns' => 'Primary Dns',
            'secondary_dns' => 'Secondary Dns',
            'wins_server' => 'Wins Server',
            'notes' => 'Notes',
        ];
    }
}
