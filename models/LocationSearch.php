<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Location;
use yii\helpers\ArrayHelper;
use app\models\UserHasCustomer;

/**
 * LocationSearch represents the model behind the search form about `app\models\Location`.
 */
class LocationSearch extends Location
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'customer_id', 'deleted', 'shipping_deliverymethod'], 'integer'],
            [['storename', 'storenum', 'address', 'address2', 'country', 'city', 'state', 'zipcode', 'phone', 'email', 'default_accountnumber', 'created_at'], 'safe'],
        ];
    }

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
    	$customers = ArrayHelper::getColumn(UserHasCustomer::find()->where(['userid'=>Yii::$app->user->id])->asArray()->all(), 'customerid');
    	
        $query = Location::find()->where(['customer_id'=>$customers]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }
        
        $_searchstring = $this->address;
        
        //var_dump($_searchstring);
        
		if(!empty($_searchstring))
		{
	        $query->andFilterWhere(['like', 'storename', $_searchstring])
	            ->orFilterWhere(['like', 'storenum', $_searchstring])
	            ->orFilterWhere(['like', 'address', $_searchstring])
	            ->orFilterWhere(['like', 'address2', $_searchstring])
	            ->orFilterWhere(['like', 'country', $_searchstring])
	            ->orFilterWhere(['like', 'city', $_searchstring])
	            ->orFilterWhere(['like', 'state', $_searchstring])
	            ->orFilterWhere(['like', 'zipcode', $_searchstring])
	            ->orFilterWhere(['like', 'phone', $_searchstring])
	            ->orFilterWhere(['like', 'email', $_searchstring]);
		}
       
        $query->andFilterWhere([
        		'deleted'=>0,
        		'customer_id'=>$customers
        		]);

        return $dataProvider;
    }
}
