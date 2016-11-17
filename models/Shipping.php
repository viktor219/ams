<?php

namespace app\models;

use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use Yii;

/**
 * 
 */
class Shipping extends ActiveRecord {

    /**
     * Declares the name of the database table associated with this AR class.
     *
     * @return string
     */
    public static function tableName() {
        return '{{%shipments}}';
    }

    /**
     * Returns the validation rules for attributes.
     *
     * @return array
     */
    public function rules() {
        return [
            [['location_id', 'customer_id'], 'required'],
            [['location_id', 'customer_id','type'], 'safe'],
        ];
    }

}
