<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "{{%customers}}".
 *
 * @property integer $id
 * @property string $code
 * @property integer $customer_administrator
 * @property integer $parent_id
 * @property integer $owner_id
 * @property string $firstname
 * @property string $lastname
 * @property string $companyname
 * @property string $phone
 * @property string $phone2
 * @property string $email
 * @property string $fax
 * @property integer $trackincomingserials
 * @property integer $requireserialphoto
 * @property integer $requireordernumber
 * @property integer $allownewcustomerorder
 * @property integer $allowdirectshippingreq
 * @property integer $allowweeklyautorderreq
 * @property integer $allowincomingoutshchedule
 * @property integer $temporaryinventorystatus
 * @property integer $requirestorenumber
 * @property integer $requirepalletcount
 * @property integer $requireboxcount
 * @property integer $requirelanenumber
 * @property integer $requirelabelmodel
 * @property integer $requirelabelbox
 * @property integer $requirelabelpallet
 * @property integer $picture_id
 * @property integer $vert_picture_id
 * @property string $defaultreceivinglocation
 * @property integer $defaultshippinglocation
 * @property integer $defaultbillinglocation
 * @property string $created_at
 * @property string $modified_at
 */
class Customer extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%customers}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['code', 'customer_administrator', 'payment_terms_id', 'parent_id', 'owner_id', 'billcustomershipping', 'defaultshippingchoice', 'trackincomingserials', 'requireserialphoto', 'requireordernumber', 'allownewcustomerorder', 'allowdirectshippingreq', 'allowweeklyautorderreq', 'allowincomingoutshchedule', 'temporaryinventorystatus', 'customerstoreinventory', 'requirestorenumber', 'requirepalletcount', 'requireboxcount', 'requirelanenumber', 'requirelabelmodel', 'requirelabelbox', 'requirelabelpallet', 'picture_id', 'vert_picture_id', 'defaultshippinglocation', 'defaultbillinglocation', 'firstname', 'lastname', 'companyname', 'phone', 'phone2', 'email', 'fax', 'defaultreceivinglocation', 'created_at', 'modified_at'], 'safe']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'code' => 'Code',
            'customer_administrator' => 'Customer Administrator',
            'parent_id' => 'Parent ID',
            'owner_id' => 'Owner ID',
            'firstname' => 'Firstname',
            'lastname' => 'Lastname',
            'companyname' => 'Companyname',
            'phone' => 'Phone',
            'phone2' => 'Phone2',
            'email' => 'Email',
            'fax' => 'Fax',
            'trackincomingserials' => 'Trackincomingserials',
            'requireserialphoto' => 'Requireserialphoto',
            'requireordernumber' => 'Requireordernumber',
            'allownewcustomerorder' => 'Allownewcustomerorder',
            'allowdirectshippingreq' => 'Allowdirectshippingreq',
            'allowweeklyautorderreq' => 'Allowweeklyautorderreq',
            'allowincomingoutshchedule' => 'Allowincomingoutshchedule',
            'temporaryinventorystatus' => 'Temporaryinventorystatus',
            'requirestorenumber' => 'Requirestorenumber',
            'requirepalletcount' => 'Requirepalletcount',
            'requireboxcount' => 'Requireboxcount',
            'requirelanenumber' => 'Requirelanenumber',
            'requirelabelmodel' => 'Requirelabelmodel',
            'requirelabelbox' => 'Requirelabelbox',
            'requirelabelpallet' => 'Requirelabelpallet',
            'picture_id' => 'Picture ID',
            'vert_picture_id' => 'Vert Picture ID',
            'defaultreceivinglocation' => 'Defaultreceivinglocation',
            'defaultshippinglocation' => 'Defaultshippinglocation',
            'defaultbillinglocation' => 'Defaultbillinglocation',
            'created_at' => 'Created At',
            'modified_at' => 'Modified At',
        ];
    }
	
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUserHasCustomer()
    {
        return $this->hasOne(UserHasCustomer::className(), ['customerid' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUsers()
    {
        return $this->hasMany(Users::className(), ['id' => 'userid'])->viaTable('{{%user_has_customer}}', ['customerid' => 'id']);
    }
    /**
     * @return \yii\db\ActiveQuery
     */	
    public function getLocations() {
        return $this->hasMany(Location::className(), ['customer_id' => 'id']);
    }	
}
