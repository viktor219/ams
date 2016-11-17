<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "lv_users".
 *
 * @property integer $id
 * @property string $firstname
 * @property string $lastname
 * @property string $email
 * @property string $username
 * @property string $hash_password
 * @property integer $usertype
 * @property string $token
 * @property integer $deleted
 * @property integer $department
 * @property integer $division_id
 * @property string $default_mac_address
 * @property string $default_ip_address
 * @property string $last_login
 * @property integer $password_changed
 * @property string $created_at
 * @property string $modified_at
 *
 * @property UserHasCustomer $userHasCustomer
 * @property Customers[] $customers
 */
class Users extends \yii\db\ActiveRecord
{	
	
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'lv_users';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['firstname', 'lastname', 'email', 'username', 'hash_password', 'default_ip_address', 'token', 'default_mac_address', 'usertype', 'division_id', 'department', 'password_changed', 'deleted', 'last_login', 'division_id', 'created_at', 'modified_at'], 'safe'],
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
            'email' => 'Email',
            'username' => 'Username',
            'hash_password' => 'Hash Password',
            'usertype' => 'Usertype',
            'token' => 'Token',
            'division_id' => 'Division ID',
            'department' => 'Department',
            'default_mac_address' => 'Default Mac Address',
            'default_ip_address' => 'Default Ip Address',
            'last_login' => 'Last Login',
            'password_changed' => 'Password Changed',
			'deleted' => 'Deleted',
            'created_at' => 'Created At',
            'modified_at' => 'Modified At',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUserHasCustomer()
    {
        return $this->hasOne(UserHasCustomer::className(), ['userid' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCustomers()
    {
        return $this->hasMany(Customers::className(), ['id' => 'customerid'])->viaTable('lv_user_has_customer', ['userid' => 'id']);
    }
	
	
	public static function findLocation($ip = null)
	{
		if($ip===null)
			$ip = Yii::$app->getRequest()->getUserIP();
		
		$geoplugin = new GeoPlugin;
		$geoplugin->locate();
		//var_dump($geoplugin);
		//return location instance of user.
		return $geoplugin;
	}
	
	public static function isProxy()
	{
		$return = false;
		
		$proxy_headers = array(
			'HTTP_VIA',
			'HTTP_X_FORWARDED_FOR',
			'HTTP_FORWARDED_FOR',
			'HTTP_X_FORWARDED',
			'HTTP_FORWARDED',
			'HTTP_CLIENT_IP',
			'HTTP_FORWARDED_FOR_IP',
			'VIA',
			'X_FORWARDED_FOR',
			'FORWARDED_FOR',
			'X_FORWARDED',
			'FORWARDED',
			'CLIENT_IP',
			'FORWARDED_FOR_IP',
			'HTTP_PROXY_CONNECTION'
		);
		
		foreach($proxy_headers as $x){
			if (isset($_SERVER[$x]))
				$return = true;
		}
		//
		return $return;
	}
        public function afterSave($insert, $changedAttributes){
            if(isset(Yii::$app->user->id) && Yii::$app->user->id){
                $recentActivity = New Recentactivity;
                $recentActivity->type = array_search('User', Recentactivity::$type);
                $recentActivity->pk = $this->id;
                $recentActivity->customer_id = "";
                $recentActivity->user_id = Yii::$app->user->id;
                $recentActivity->created_at = date('Y-m-d H:i:s');
                if($insert){
                    $recentActivity->is_new = 1;
                    $recentActivity->save(false);
                } elseif(count($changedAttributes) && !isset($changedAttributes['last_login'])){
                    $recentActivity->is_new = 0;
                    if(isset($changedAttributes['usertype'])){
                        $recentActivity->usertype = $changedAttributes['usertype'];
                    }
                    $recentActivity->save(false);
                }
                parent::afterSave($insert, $changedAttributes);
            }
        }
}
