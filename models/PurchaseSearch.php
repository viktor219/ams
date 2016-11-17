<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Purchase;
use yii\helpers\ArrayHelper;

/**
 * PurchaseSearch represents the model behind the search form about `app\models\Purchase`.
 */
class PurchaseSearch extends Purchase
{

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
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
        $query = Purchase::find()->andFilterWhere(['deleted'=>0]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        	'pagination' => ['pageSize' => 15],
        ]);

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        $vendors = ArrayHelper::getColumn(Vendor::find()->select('id')->where(['like', 'vendorname', $this->number_generated])->orWhere(['like', 'vendorid', $this->number_generated])->all(), 'id');
        
        if(count($vendors)>0){
        	$query->andFilterWhere(['vendor_id'=>$vendors, 'deleted'=>0]);
        	
        	$query->orFilterWhere(['like', 'number_generated', $this->number_generated]);
        } else 
        	$query->andFilterWhere(['like', 'number_generated', $this->number_generated, 'deleted'=>0]);
        

        return $dataProvider;
    }
}