<?php

namespace app\models;

use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\data\SqlDataProvider;
use Yii;

/**
 * ShippingSearch represents the model behind the search form for common\models\User.
 */
class ShippingSearch extends Shipping {

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
            [['customer_id'], 'safe'],
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
    public function search($params, $customer) {

        $query = Shipping::find()->where(['customer_id' => $customer]);

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

        $query->andFilterWhere([
            'id' => $this->id,
            'created_at' => $this->created_at,
        ]);
        return $dataProvider;
    }

}
