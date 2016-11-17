<?php
namespace app\models;

use Yii;
use yii\base\NotSupportedException;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\web\IdentityInterface;
use app\vendor\GeoPlugin;

/**
 * User model
 *
 * @property integer $id
 * @property string $username
 * @property string $password_hash
 * @property string $password_reset_token
 * @property string $email
 * @property string $auth_key
 * @property integer $status
 * @property integer $created_at
 * @property integer $updated_at
 * @property string $password write-only password
 */
class User extends ActiveRecord implements IdentityInterface
{
    const STATUS_DELETED = 0;
    const STATUS_ACTIVE = 1;
    public $updated_at;
    public $created_at;
	public $captcha;
	public $ConfirmPassword;
	public $authKey;
	//user types :
	const TYPE_CUSTOMER = 1;
	const TYPE_RECEIVING = 2;
	const TYPE_TECHNICIAN = 3;
	const TYPE_SHIPPING = 4;
	const TYPE_BILLING = 5;
	const TYPE_SALES = 6;
	const TYPE_ADMIN = 7;
	const TYPE_CUSTOMER_ADMIN = 8;
	const REPRESENTATIVE = 9;
	
	public static $status = array(
		1 => 'Customer',
		2 => 'Receiving',
		3 => 'Technician',
		4 => 'Shipping',
		5 => 'Billing',
		6 => 'Sales',
		7 => 'Admin',
		8 => 'Purchasing',
		9 => 'Representative'
	);

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%users}}';
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            TimestampBehavior::className(),
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
			['username', 'filter', 'filter' => 'trim'],
			['username', 'required'],
			['username', 'unique', 'message' => 'This username has already been taken.'],
			[['firstname', 'lastname', 'username', 'password'], 'string', 'max' => 255],	

			['email', 'filter', 'filter' => 'trim'],
			['email', 'required'],
			['email', 'email'],
			['email', 'unique', 'message' => 'This email address has already been taken.'],
			[['email'], 'string', 'max' => 100],
			
			[['usertype', 'department'], 'string', 'max' => 11],

			['passwd', 'required'],
			['ConfirmPassword', 'compare', 'compareAttribute' => 'passwd'],
			['passwd', 'string', 'min' => 6, 'max' => 255],
			['ConfirmPassword','safe'],
			
            [['last_login', 'created', 'suspend'], 'safe'],
            [['role', 'picture_id'], 'integer'],
			[['captcha'], 'captcha'],
        ];
    }

    /**
     * @inheritdoc
     */
    public static function findIdentity($id) {
    	$dbUser = self::find()
    	->where([
    			"id" => $id
    			])
    			->one();
    	if (!count($dbUser)) {
    		return null;
    	}
    	return new static($dbUser);
    }
    
    /**
     * @inheritdoc
     */
    public static function findIdentityByAccessToken($token, $userType = null) {
    
    	$dbUser = self::find()
    	->where(["accessToken" => $token])
    	->one();
    	if (!count($dbUser)) {
    		return null;
    	}
    	return new static($dbUser);
    }
    
    /**
     * Finds user by username
     *
     * @param  string      $username
     * @return static|null
     */
    public static function findByUsername($username) {
    	$dbUser = User::find()
	    	->where(["email" => $username])
	    	->orWhere('username = :name', [':name' => $username])
	    	->one();
    	if (!count($dbUser)) {
    		return null;
    	}
                 // Generate a login token and save it in the DB
        $dbUser->accessToken = sha1(uniqid(mt_rand(), true));
        $dbUser->save(false);

       $cookie = new \yii\web\Cookie([
            'name' => 'loginCookie',
            'value' => $dbUser->accessToken,
            'expire' => time()+60*60*24*365,
	]);
        Yii::$app->response->cookies->add($cookie);
    	return new static($dbUser);
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
            'username' => 'Username',
            'password' => 'Password',
            'usertype' => 'Usertype',
            'customer1' => 'Customer1',
            'customer2' => 'Customer2',
            'customer3' => 'Customer3',
            'customer4' => 'Customer4',
            'customer5' => 'Customer5',
            'customer6' => 'Customer6',
            'customer7' => 'Customer7',
            'customer8' => 'Customer8',
            'department' => 'Department',
            'email' => 'Email',
            'enhanced' => 'Enhanced',
        ];
    }
    	
    /**
     * Finds user by password reset token
     *
     * @param string $token password reset token
     * @return static|null
     */
    public static function findByPasswordResetToken($token)
    {
        if (!static::isPasswordResetTokenValid($token)) {
            return null;
        }

        return static::findOne([
            'password_reset_token' => $token,
            'status' => self::STATUS_ACTIVE,
        ]);
    }

    /**
     * Finds out if password reset token is valid
     *
     * @param string $token password reset token
     * @return boolean
     */
    public static function isPasswordResetTokenValid($token)
    {
        if (empty($token)) {
            return false;
        }

        $timestamp = (int) substr($token, strrpos($token, '_') + 1);
        $expire = Yii::$app->params['user.passwordResetTokenExpire'];
        return $timestamp + $expire >= time();
    }

    /**
     * @inheritdoc
     */
    public function getId() {
    	return $this->id;
    }

    /**
     * @inheritdoc
     */
    public function getAuthKey() {
    	return $this->authKey;
    }
    
    public function beforeValidate()
    {
    	if($this->isNewRecord){
	    	$this->created=date('Y-m-d H:i:s');
    	}
    	return parent::beforeValidate();
    }
    
    public function beforeSave($insert)
    {
    	//$this->hash_password = $this->validatePassword($this->hash_password);
    	return parent::beforeSave($insert);
    }

    /**
     * Generates password hash from password and sets it to the model
     *
     * @param string $password
     */
    public function setPassword($password)
    {
        //$this->hash_password = Yii::$app->security->generatePasswordHash($password);
    	$this->hash_password = md5($password);
    }
    
    /**
     * @inheritdoc
     */
    public function validateAuthKey($authKey)
    {
    	return $this->authKey === $authKey;
    }
    
    /**
     * Validates password
     *
     * @param  string  $password password to validate
     * @return boolean if password provided is valid for current user
     */
    public function validatePassword($password)
    {
    	return $this->hash_password === $password;
    }

    /**
     * Generates "remember me" authentication key
     */
    public function generateAuthKey()
    {
        $this->auth_key = Yii::$app->security->generateRandomString();
    }

    /**
     * Generates new password reset token
     */
    public function generatePasswordResetToken()
    {
        $this->password_reset_token = Yii::$app->security->generateRandomString() . '_' . time();
    }

    /**
     * Removes password reset token
     */
    public function removePasswordResetToken()
    {
        $this->password_reset_token = null;
    }

}
