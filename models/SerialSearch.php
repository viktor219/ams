<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\data\SqlDataProvider;
use app\models\Item;
use app\models\UserHasCustomer;
use app\models\Customer;
use yii\helpers\ArrayHelper;

/**
 * SerialSearch represents the model behind the search form about `app\models\Item`.
 */
class SerialSearch extends Item
{
	
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
			[['notes'], 'string'],
            [['owner_id', 'picked', 'package_optionid', 'conditionid', 'received', 'shipped', 'returned', 'labelprinted', 'lastupdated', 'receiving_pallet', 'trackinglink', 'requested', 'prioritysort', 'shippingpallet', 'status', 'serial', 'lane', 'model', 'customer', 'location', 'trackingnumber', 'requestedlocation', 'ordernumber', 'terminalnum', 'incomingpalletnumber', 'incomingboxnumber', 'created_at', 'modified_at'], 'safe'],
        ];
    }
    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Item::scenarios();
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
		$connection = Yii::$app->getDb();
		
		$this->load($params);
		 $customer = UserHasCustomer::find()->where(['userid'=>Yii::$app->user->id])->one()->customerid;
                $customer = Customer::findOne($customer);
                $query = "SELECT lv_items.* FROM `lv_items` INNER JOIN lv_models on lv_models.id = lv_items.model WHERE  customer = ".$customer->id." ORDER BY serial";        
        if (empty($this->serial)) {
            $countSql = "SELECT count(*) as total FROM `lv_items` INNER JOIN lv_models on lv_models.id = lv_items.model WHERE  customer = ".$customer->id;
            $command = $connection->createCommand($countSql);
            $count = $command->queryColumn()[0];
            $dataProvider = new SqlDataProvider([
                        'sql' => $query,
			'totalCount' => $count,
			'pagination' => ['pageSize' => 10],
            ]);
            return $dataProvider;
        }
            $searchString = '%'.$this->serial.'%';
            $_sort_string = $this->serial.'%';
            $sql = 'SELECT lv_items.* FROM `lv_items` INNER JOIN lv_models on lv_models.id = lv_items.model WHERE  customer = '.$customer->id.' and (serial like "'.$searchString.'" or tagnum like "'.$searchString.'") ORDER BY
                CASE
                  WHEN serial LIKE "'.$_sort_string.'" THEN 1
                  ELSE 2
                END';
		
		$command = $connection->createCommand($sql);
		
		$count = count($command->queryAll());
		
		$dataProvider = new SqlDataProvider([
                    'sql' => $sql,
			'totalCount' => $count,
			'pagination' => ['pageSize' => 10],
                ]);	

        return $dataProvider;
    }
    
    public function searchInProgress($params)
    {
    	$connection = Yii::$app->getDb();
    
    	$this->load($params);
    	
    	$query = "SELECT lv_items.* FROM `lv_items` INNER JOIN lv_models on lv_models.id = lv_items.model WHERE status IN (".implode(',', array_keys(Item::$inprogressstatus)).", ".implode(',', array_keys(Item::$shippingallstatus)).") AND ordernumber = '". $this->ordernumber ."' ORDER BY serial";
    	//echo $query;
    	if (empty($this->serial)) {
    		$countSql = "SELECT count(*) as total FROM `lv_items` INNER JOIN lv_models on lv_models.id = lv_items.model WHERE status IN (".implode(',', array_keys(Item::$inprogressstatus)).", ".implode(',', array_keys(Item::$shippingallstatus)).") AND ordernumber = '". $this->ordernumber ."'";
    		$command = $connection->createCommand($countSql);
    		$count = $command->queryColumn()[0];
    		$dataProvider = new SqlDataProvider([
    				'sql' => $query,
    				'totalCount' => $count,
    				'pagination' => ['pageSize' => 10],
    				]);
    		return $dataProvider;
    	}
    	$searchString = '%'.$this->serial.'%';
    	$_sort_string = $this->serial.'%';
    	$sql = 'SELECT lv_items.* FROM `lv_items` INNER JOIN lv_models on lv_models.id = lv_items.model WHERE status IN ('.implode(',', array_keys(Item::$inprogressstatus)).', '.implode(',', array_keys(Item::$shippingallstatus)).') AND ordernumber = "'. $this->ordernumber .'" and serial like "'.$searchString.'" ORDER BY
                CASE
                  WHEN serial LIKE "'.$_sort_string.'" THEN 1
                  ELSE 2
                END';
    //echo $sql;
    	$command = $connection->createCommand($sql);
    
    	$count = count($command->queryAll());
    
    	$dataProvider = new SqlDataProvider([
    			'sql' => $sql,
    			'totalCount' => $count,
    			'pagination' => ['pageSize' => 10],
    			]);
    
    	return $dataProvider;
    }
}