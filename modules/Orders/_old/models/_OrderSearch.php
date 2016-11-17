<?php

namespace app\modules\Orders\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\modules\Orders\models\Order;
use app\models\Customer;
use yii\helpers\ArrayHelper;
use app\models\User;
use app\models\UserHasCustomer;

/**
 * OrderSearch represents the model behind the search form about `app\modules\Orders\models\Order`.
 */
class OrderSearch extends Order
{
    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = Order::find()->where(['trackingnumber' => NULL]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        	'pagination' => ['pageSize' => 15],
        ]);

        if (!($this->load($params) && $this->validate())) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        $customers = ArrayHelper::getColumn(Customer::find()->select('id')->where(['like', 'code', $this->number_generated])->orWhere(['like', 'companyname', $this->number_generated])->all(), 'id');
        
        if(Yii::$app->user->identity->usertype == USER::REPRESENTATIVE){
            
                    $rep_cust = ArrayHelper::getColumn(UserHasCustomer::find()->where(['userid'=>Yii::$app->user->id])->all(), 'customerid');
                    $customers = ArrayHelper::getColumn(Customer::find()->select('id')
                            ->where(['id' => $rep_cust, 'deleted' => 0])
                            ->orWhere(['like', 'code', $this->number_generated])
                            ->orWhere(['like', 'companyname', $this->number_generated])
                            ->asArray()
                            ->all(), 'id');
        }
        //print_r($customers);
        if(count($customers)>0){
        	$query->andFilterWhere(['customer_id'=>$customers, 'deleted' => 0]);
        	$query->orFilterWhere(['like', 'number_generated', $this->number_generated]);
        	$query->orFilterWhere(['like', 'customer_po', $this->number_generated]);
        	$query->orFilterWhere(['like', 'enduser_po', $this->number_generated]);   	
        }
        else
        {
        	$query->andFilterWhere(['like', 'number_generated', $this->number_generated, 'deleted' => 0]);
        	$query->orFilterWhere(['like', 'customer_po', $this->number_generated]);
        	$query->orFilterWhere(['like', 'enduser_po', $this->number_generated]);
        }

        return $dataProvider;
    }
}