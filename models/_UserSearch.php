<?php

namespace app\models;

use app\models\Users;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\data\SqlDataProvider;
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
//        $query = User::find();
//
//        $dataProvider = new ActiveDataProvider([
//            'query' => $query,
//            'sort' => ['defaultOrder' => ['firstname' => SORT_ASC]],
//            'pagination' => [
//                'pageSize' => $this->_pageSize,
//            ]
//        ]);
//
//        if (!($this->load($params) && $this->validate())) {
//            return $dataProvider;
//        }
//
//        $query->andFilterWhere(['like', 'firstname', $this->firstname])
//                ->orFilterWhere(['like', 'lastname', $this->lastname])
//                ->orFilterWhere(['like', 'username', $this->username])
//                ->orFilterWhere(['like', 'email', $this->email]);
        $_search_string = '"%'.$this->firstname . '%"';
        $_sort_string = '"'.$this->firstname . '%"';
        $sql = 'SELECT * FROM `lv_users` WHERE lv_users.firstname like '.$_search_string.' or lv_users.lastname like '.$_search_string.' or lv_users.email like '.$_search_string.' or lv_users.username like '.$_search_string.' ORDER BY
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
