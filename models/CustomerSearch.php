<?php

namespace app\models;

use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\data\SqlDataProvider;
use Yii;
use app\models\UserHasCustomer;
use yii\helpers\ArrayHelper;

/**
 * UserSearch represents the model behind the search form for common\models\User.
 */
class CustomerSearch extends Customer {

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
    /*public function rules() {
        return [
        ];
    }
*/
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
        if(Yii::$app->user->identity->usertype != 1)
                $query = Customer::find();
        else {
                $customers = ArrayHelper::getColumn(UserHasCustomer::find()->where(['userid'=>Yii::$app->user->id])->asArray()->all(), 'customerid');			
                $query = Customer::find()->where(['id'=>$customers]);
        }

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => $this->_pageSize,
            ],
            'sort'=> ['defaultOrder' => ['code'=>SORT_ASC]]
        ]);

        if (!($this->load($params) && $this->validate())) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');            
            return $dataProvider;
        }
        /*$query->andFilterWhere([
            'id' => $this->id,
            'created_at' => $this->created_at,
            'updated_at' => $this->modified_at,
        ]);*/

//        $query->andFilterWhere(['like', 'code', $this->code])
//        	->orFilterWhere(['like', 'companyname', $this->companyname])
//                ->orFilterWhere(['like', 'firstname', $this->firstname])
//                ->orFilterWhere(['like', 'lastname', $this->lastname]);
        
        $_search_string = '"%'.$this->firstname . '%"';
        $_sort_string = '"'.$this->firstname . '%"';
        if(Yii::$app->user->identity->usertype != 1)
            //$_default_condition = 'parent_id is NULL';
            $_default_condition = '';
        else {
            $customers = ArrayHelper::getColumn(UserHasCustomer::find()->where(['userid'=>Yii::$app->user->id])->asArray()->all(), 'customerid');
            $_default_condition = 'id = '.$customers.' AND ';
        }
        $sql = 'SELECT * FROM `lv_customers` WHERE '.$_default_condition.'(lv_customers.firstname like '.$_search_string.' or lv_customers.lastname like '.$_search_string.' or lv_customers.companyname like '.$_search_string.' or lv_customers.code like '.$_search_string.') ORDER BY
            CASE
              WHEN firstname LIKE '.$_sort_string.' THEN 2
              WHEN lastname LIKE '.$_sort_string.' THEN 4
                  WHEN companyname LIKE '.$_sort_string.' THEN 1
                  WHEN code LIKE '.$_sort_string.' THEN 3
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

    /**
     * 
     * @param type $params
     * @return ActiveDataProvider
     */
    public function searchShipment($params) {

        $query = Customer::find()->where(['parent_id' => null]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => ['defaultOrder' => ['id' => SORT_DESC]],
            'pagination' => [
                'pageSize' => $this->_pageSize,
            ]
        ]);

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        return $dataProvider;
    }

}