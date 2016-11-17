<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "lv_user_log_tracking".
 *
 * @property integer $id
 * @property integer $userid
 * @property integer $location_id
 * @property string $mac_address
 * @property string $ip_address
 * @property string $real_ip_address
 * @property string $browser
 * @property integer $using_proxy
 * @property string $device_type
 * @property string $created_at
 */
class UserLogTracking extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'lv_user_log_tracking';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['userid'], 'integer'],
            [['location_id', 'using_proxy', 'created_at', 'mac_address', 'ip_address', 'real_ip_address', 'browser', 'device_type', 'status'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'userid' => 'User',
            'location_id' => 'Location',
            'mac_address' => 'Mac Address',
            'ip_address' => 'Ip Address',
            'real_ip_address' => 'Real Ip Address',
            'browser' => 'Browser',
            'using_proxy' => 'Using Proxy',
            'device_type' => 'Device Type',
            'created_at' => 'Logged At',
        ];
    }
}
