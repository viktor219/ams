<?php

namespace app\models;

use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use app\models\Customer;
use Yii;

/**
 * 
 */
class UserCreate extends ActiveRecord {

    /**
     * Declares the name of the database table associated with this AR class.
     *
     * @return string
     */
    public static function tableName() {
        return '{{%users}}';
    }

    /**
     * Returns the validation rules for attributes.
     *
     * @return array
     */
    public function rules() {
        return [
            //[['firstname', 'lastname', 'username', 'email', 'usertype', 'hash_password'], 'required'],
            [['firstname', 'lastname', 'username', 'email', 'usertype', 'picture_id', 'hash_password', 'department', 'created_at', 'division_id'], 'safe'],
        ];
    }
    public function afterSave($insert, $changedAttributes){
        parent::afterSave($insert, $changedAttributes);
        $recentActivity = New Recentactivity;
        $recentActivity->type = array_search('User', Recentactivity::$type);
        $recentActivity->pk = $this->id;
        $recentActivity->customer_id = "";
        $recentActivity->itemscount = 0;
        $recentActivity->user_id = Yii::$app->user->id;
        $recentActivity->created_at = date('Y-m-d H:i:s');
        if($insert){
            $recentActivity->is_new = 1;
            $recentActivity->save(false);
        } elseif(count($changedAttributes)){
            $recentActivity->is_new = 0;
            if(isset($changedAttributes['usertype'])){
                $recentActivity->usertype = $changedAttributes['usertype'];
            }
            $recentActivity->save(false);
        }
    }

}
