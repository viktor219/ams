<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Vendor;

/**
 * VendorSearch represents the model behind the search form about `app\models\Vendor`.
 */
class VendorSearch extends Vendor
{
	public function rules()
	{
		return [
		[['vendorid', 'vendorname', 'address_line_1', 'address_line_2', 'city', 'zip', 'state', 'contact', 'telephone_1', 'telephone_2', 'fax', 'taxidno', 'terms', 'accountno', 'email', 'website', 'last_inv_amt', 'notes', 'date_joined'], 'safe'],
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
        $query = Vendor::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }
        
        $searchstring = $this->vendorid;

        $query->andFilterWhere(['like', 'vendorid', $searchstring])
            ->orFilterWhere(['like', 'vendorname', $searchstring])
            ->orFilterWhere(['like', 'address_line_1', $searchstring])
            ->orFilterWhere(['like', 'address_line_2', $searchstring])
            ->orFilterWhere(['like', 'city', $searchstring])
            ->orFilterWhere(['like', 'zip', $searchstring])
            ->orFilterWhere(['like', 'state', $searchstring])
            ->orFilterWhere(['like', 'contact', $searchstring])
            ->orFilterWhere(['like', 'telephone_1', $searchstring])
            ->orFilterWhere(['like', 'telephone_2', $searchstring])
            ->orFilterWhere(['like', 'fax', $searchstring])
            ->orFilterWhere(['like', 'email', $searchstring])
            ->orFilterWhere(['like', 'notes', $searchstring]);

        return $dataProvider;
    }
}