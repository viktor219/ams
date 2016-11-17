<?php

namespace app\models;

use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use app\models\Customer;
use Yii;

/**
 * 
 */
class UserHasCustomer extends ActiveRecord {

    /**
     * Declares the name of the database table associated with this AR class.
     *
     * @return string
     */
    public static function tableName() {
        return '{{%user_has_customer}}';
    }

    /**
     * Returns the validation rules for attributes.
     *
     * @return array
     */
    public function rules() {
        return [
            [['userid', 'customerid'], 'required'],
            [['userid', 'customerid'], 'safe'],
        ];
    }

}
