<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ArrayDataProvider;
use yii\data\SqlDataProvider;
use app\models\Inventory;
use app\models\Manufacturer;
use yii\helpers\ArrayHelper;

/**
 * InventorySearch represents the model behind the search form about `app\models\Inventory`.
 */
class InventorySearch extends Inventory
{
	public $partnum;
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'serialized', 'quote'], 'integer'],
            [['aei', 'descrip', 'image_id', 'manufacturer', 'department', 'category_id', 'palletqtylimit', 'stripcharacters', 'checkit', 'frupartnum', 'manpartnum', 'storespecific', 'created_at', 'modified_at'], 'safe'],
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
		$connection = Yii::$app->getDb();
		
		$this->load($params);
		
        $query = "SELECT * FROM lv_models where deleted = 0";
		
		$command = $connection->createCommand($query);
		
		$count = count($command->queryAll());

        $dataProvider = new SqlDataProvider([
            'sql' => $query,
			'totalCount' => $count,
			'pagination' => ['pageSize' => 10],
        ]);

//        if (!$this->validate()) {
//            // uncomment the following line if you do not want to return any records when validation fails
//            // $query->where('0=1');
//            return $dataProvider;
//        }
		
		$search_string = trim($this->descrip);
//		ini_set('mongo.long_as_object', 1);
//                $m = new \Mongo("mongodb://localhost");
//                $db = $m->ams;
//                $returnData = array();
                $search = trim($search_string);
                $searchStrings = explode(" ", $search);
                if(count($searchStrings)> 1){
                    $search = '';
                    foreach($searchStrings as $string){
                        $search .= "(?=.*".$string.")";
                    }
                } else {
                    $search = str_replace(" ",".*",$search);
                    $search = ".*".$search.".*";
                }
//                $response= $db->execute('return db.inventorymodels.find({$or : [{modelname: '.$search.'}, {aei: '.$search.'}, {frupartnum: '.$search.'}, {manpartnum: '.$search.'}, {partnumber: '.$search.'}] }).sort({ "modelname":1, "aei":1, "frupartnum":1, "manpartnum":1 , "partnumber": 1}).toArray();'); 
//                if($search_string!=''){
//                    usort($response['retval'], function($a, $b) use($search_string){
//                        if(strpos($a['modelname'], trim($search_string)) === 0){
//                            $sort = 1;
//                        } elseif(strpos($b['modelname'], trim($search_string)) === 0){
//                            $sort = 1;
//                        } else {
//                            $sort = 2;
//                        }
//                        return $sort;
//                    });
//                }
                $condition = $customers = array();
                $condition = array(
                    '$or' => array(
                        array(
                            "modelname" => array('$regex' => $search, '$options' => 'i'), 
                        ),
                        array(
                            "aei" => array('$regex' => $search, '$options' => 'i'), 
                        ),
                        array(
                            "frupartnum" => array('$regex' => $search, '$options' => 'i'),
                        ),
                        array(
                            "manpartnum" => array('$regex' => $search, '$options' => 'i')
                        ),
                        array(
                            "partnumber" => array('$regex' => $search, '$options' => 'i')
                        )
                    ),                    
                );
                $customers = array();
                if(Yii::$app->user->identity->usertype == User::REPRESENTATIVE || Yii::$app->user->identity->usertype==User::TYPE_CUSTOMER){
                    $customers = ArrayHelper::getColumn(UserHasCustomer::find()->where(['userid'=>Yii::$app->user->id])->asArray()->all(), 'customerid');
                    if(!count($customers) ){
                        $customers = array(-1);
                    }
                }
//                $condition['$and'] = array(
//                        array('customer' => array('$in' => $customers))
//                    );
                $cond['deleted'] = "0";
                if(count($customers)){
                    $cond['customer'] = array('$in' => $customers);
                }
                $condition['$and'] = array($cond);
                $sort = array('modelname' => 1, 'aei' => 1, 'frupartnum' => 1, 'manpartnum' => 1, "partnumber" => 1);
                $models = Yii::$app->common->getInventory($condition, $sort);
                if($search_string!=''){
                    usort($models, function($a, $b) use($search_string){
                        if(strpos($a['modelname'], trim($search_string)) === 0){
                            $sort = 1;
                        } elseif(strpos($b['modelname'], trim($search_string)) === 0){
                            $sort = 1;
                        } else {
                            $sort = 2;
                        }
                        return $sort;
                    });
                }
                $dataProvider = new ArrayDataProvider([
                'allModels' => $models,
//                    'sort' => [
//                        'attributes' => ['id', 'username', 'email'],
//                    ],
                    'pagination' => [
                        'pageSize' => 15,
                    ],
                ]);
//		$sql = "SELECT 
//                        lv_models.descrip, 
//                        lv_models.image_id, 
//                        lv_models.assembly, 
//                        lv_models.aei, 
//                        lv_models.frupartnum, 
//                        lv_models.manpartnum, 
//                        lv_manufacturers.name,
//                        lv_departements.name as department,
//                        lv_models.id,
//                        lv_models.category_id,
//                        lv_medias.filename";
//                if(!empty($search_string)){
//                    $sql .= ", MATCH(lv_models.descrip, lv_models.aei, lv_models.frupartnum, lv_models.manpartnum) AGAINST('$search_string*' IN BOOLEAN MODE) as modelscore,
//                        MATCH(lv_manufacturers.name) AGAINST('$search_string*' IN BOOLEAN MODE) as mfrscore,
//                        (SELECT SUM(match(partid) AGAINST('$search_string*' IN BOOLEAN MODE)) FROM `lv_partnumbers` where model = lv_models.id) as partnumscore";
//                }
//                $sql .= ",IFNULL(p.nb_models, 0) AS nb_models,
//                        IFNULL(p.instock_qty, 0) AS instock_qty
//                        FROM lv_models 
//                        LEFT JOIN lv_manufacturers ON lv_models.manufacturer = lv_manufacturers.id 
//                        LEFT JOIN lv_departements ON lv_models.department = lv_departements.id 
//                        LEFT JOIN lv_medias ON lv_models.image_id = lv_medias.id 
//                        LEFT JOIN (
//                                SELECT model, count(id) AS nb_models, SUM(IF(status='". array_search('In Stock', Item::$status)."',1,0)) AS instock_qty
//                                FROM lv_items
//                                WHERE status IN (". array_search('In Stock', Item::$status).")
//                                GROUP BY model
//                        ) p ON (lv_models.id = p.model)";
//                if(!empty($search_string)){
//                    $sql .=" WHERE 
//                        MATCH(lv_models.descrip, lv_models.aei, lv_models.frupartnum, lv_models.manpartnum) AGAINST('$search_string*' IN BOOLEAN MODE)
//                        OR MATCH(lv_manufacturers.name) AGAINST('$search_string*' IN BOOLEAN MODE) ORDER BY (modelscore + mfrscore +  partnumscore) DESC, lv_models.aei ASC";                    
//                } else {
//                    $sql .=" ORDER BY id";
//                }
//                if(Yii::$app->user->identity->usertype == User::REPRESENTATIVE || Yii::$app->user->identity->usertype==User::TYPE_CUSTOMER){
//                    $customers = ArrayHelper::getColumn(UserHasCustomer::find()->where(['userid'=>Yii::$app->user->id])->asArray()->all(), 'customerid');
//                    if(!count($customers) ){
//                        $customers = array(-1);
//                    }
//                    $my_customers = "(".implode(",", array_map('intval', $customers)).")";
//                    $sql = "SELECT 
//                            lv_models.descrip, 
//                            lv_models.image_id, 
//                            lv_models.assembly, 
//                            lv_models.aei, 
//                            lv_models.frupartnum, 
//                            lv_models.manpartnum, 
//                            lv_manufacturers.name,
//                            lv_departements.name as department,
//                            lv_models.id,
//                            lv_models.category_id,
//                            lv_medias.filename";
//                    if(!empty($search_string)){
//                    $sql .= ", MATCH(lv_models.descrip, lv_models.aei, lv_models.frupartnum, lv_models.manpartnum) AGAINST('$search_string*' IN BOOLEAN MODE) as modelscore,
//                            MATCH(lv_manufacturers.name) AGAINST('$search_string*' IN BOOLEAN MODE) as mfrscore,
//                            (SELECT SUM(match(partid) AGAINST('$search_string*' IN BOOLEAN MODE)) FROM `lv_partnumbers` where model = lv_models.id) as partnumscore";
//                    }
//                    $sql .=", IFNULL(p.nb_models, 0) AS nb_models,
//                            IFNULL(p.instock_qty, 0) AS instock_qty
//                            FROM lv_models 
//                            LEFT JOIN lv_manufacturers ON lv_models.manufacturer = lv_manufacturers.id 
//                            LEFT JOIN lv_departements ON lv_models.department = lv_departements.id 
//                            LEFT JOIN lv_medias ON lv_models.image_id = lv_medias.id
//                            INNER JOIN lv_items on lv_items.model = lv_models.id
//                            LEFT JOIN (
//                                    SELECT model, count(id) AS nb_models, SUM(IF(status='". array_search('In Stock', Item::$status)."',1,0)) AS instock_qty
//                                    FROM lv_items
//                                    WHERE status IN (". array_search('In Stock', Item::$status).")
//                                    AND customer IN " . $my_customers . "
//                                    GROUP BY model
//                            ) p ON (lv_models.id = p.model)
//                            WHERE ";
//                    if(!empty($search_string)){
//                        $sql .=" (MATCH(lv_models.descrip, lv_models.aei, lv_models.frupartnum, lv_models.manpartnum) AGAINST('$search_string*' IN BOOLEAN MODE)
//                                OR MATCH(lv_manufacturers.name) AGAINST('$search_string*' IN BOOLEAN MODE) ) AND ";
//                        }
//                    $sql .=" customer IN " . $my_customers . " and lv_models.deleted = 0 GROUP BY lv_models.id";
//                    if(!empty($search_string)){
//                        $sql .=" ORDER BY (modelscore + mfrscore +  partnumscore) DESC, lv_models.aei ASC";
//                    } else {
//                        $sql .=" ORDER BY lv_manufacturers.name, lv_models.descrip";
//                    }
//                }	
//		$command = $connection->createCommand($sql);
//		
//		$count = count($command->queryAll());
//		
//		$dataProvider = new SqlDataProvider([
//            'sql' => $sql,
//			'totalCount' => $count,
//			'pagination' => ['pageSize' => 15],
//        ]);	

        return $dataProvider;
    }
}