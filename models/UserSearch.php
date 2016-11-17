<?php

namespace app\models;

use app\models\Users;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\data\SqlDataProvider;
use yii\helpers\ArrayHelper;
use Yii;

/**
 * UserSearch represents the model behind the search form for common\models\User.
 */
class UserSearch extends Users {

    /**
     * How many users we want to display per page.
     *
     * @var int
     */
    private $_pageSize = 10;

    /**
     * Returns the validation rules for attributes.
     *
     * @return array
     */
    public function rules() {
        
        return [
            
            [['email','username', 'firstname', 'lastname'], 'safe'],
        ];
    }

    /**
     * Returns a list of scenarios and the corresponding active attributes.
     *
     * @return array
     */
    public function scenarios() {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param  array $params
     * @return ActiveDataProvider
     */
    public function search($params) {
        $this->load($params);
        
        $_search_string = '"%'.$this->firstname . '%"';
        $_sort_string = '"'.$this->firstname . '%"';
        $sql = 'SELECT * FROM `lv_users`';
        if (Yii::$app->user->identity->usertype == User::TYPE_CUSTOMER) {
            $sql .=' Inner Join lv_user_has_customer on lv_user_has_customer.userid = lv_users.id';
        }
        $sql .=' WHERE (lv_users.firstname like '.$_search_string.' or lv_users.lastname like '.$_search_string.' or lv_users.email like '.$_search_string.' or lv_users.username like '.$_search_string.' ) and lv_users.deleted = 0';
        if (Yii::$app->user->identity->usertype == User::TYPE_CUSTOMER) {
            $customers = ArrayHelper::getColumn(UserHasCustomer::find()->where(['userid'=>Yii::$app->user->id])->asArray()->all(), 'customerid');
            if(!count($customers) ){
                $customers = array(-1);
            }
            $my_customers = "(".implode(",", array_map('intval', $customers)).")";
            $sql .=' and lv_user_has_customer.customerid IN '.$my_customers;
        }
        $sql.= ' ORDER BY
            CASE
              WHEN firstname LIKE '.$_sort_string.' THEN 1
              WHEN lastname LIKE '.$_sort_string.' THEN 2
                  WHEN username LIKE '.$_sort_string.' THEN 3
                  WHEN email LIKE '.$_sort_string.' THEN 4
              ELSE 5
            END';
        $connection = Yii::$app->getDb();
        $command = $connection->createCommand($sql);
            $count = count($command->queryAll());		
            $dataProvider = new SqlDataProvider([
                'sql' => $sql,
                'totalCount' => $count,
                'pagination' => ['pageSize' => $this->_pageSize],
            ]);
        return $dataProvider;
    }

}
