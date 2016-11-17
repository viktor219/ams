<?php

namespace app\models;

use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use app\models\Customer;
use Yii;

/**
 * 
 */
class Location extends ActiveRecord {

    /**
     * Declares the name of the database table associated with this AR class.
     *
     * @return string
     */
    public static function tableName() {
        return '{{%locations}}';
    }

    /**
     * Returns the validation rules for attributes.
     *
     * @return array
     */
    public function rules() {
        return [
            [['has_child_location', 'customer_id', 'country', 'city', 'state', 'address', 'zipcode', 'created_at','storenum', 'connection_type', 'storename','phone', 'shipping_deliverymethod', 'default_accountnumber'], 'safe'],
        ];
    }

    /**
     * 
     * @return type
     */
    public function getCustomer() {

        return $this->hasOne(Customer::className(), ['id' => 'customer_id']);
    }

    public function getCustomerName() {
        return !empty($this->customer->contactname) ? $this->customer->contactname : '';
    }
	
    /**
     * @return string 
	 * Generate 4 digits Store number.
     */
	
	public static function generateUniqueStoreNum()
	{
		$allowed_characters = array(1,2,3,4,5,6,7,8,9,0);
		$number_of_digits = 4;
		$number_of_allowed_character = count($allowed_characters);
		$unique = "";
		for($i = 1;$i <= $number_of_digits; $i++){
			$unique .= $allowed_characters[rand(0, $number_of_allowed_character - 1)];
		}		
		$unique = abs($unique);
		$gen_length = strlen($unique);
		$diff = $number_of_digits - $gen_length;
		if($diff>0)
		{
			$i=1;
			while($i<=$diff)
			{
				$unique .= rand(0, $number_of_allowed_character);
				$i++;
			}
		}
		return $unique;
	}

}
