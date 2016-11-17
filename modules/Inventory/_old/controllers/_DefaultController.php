<?php

namespace app\modules\Inventory\controllers;

use Yii;
use yii\web\Controller;
use app\models\Inventory;
use yii\data\ActiveDataProvider;
use yii\data\SqlDataProvider;
use app\models\InventorySearch;
use app\models\User;
use app\models\Partnumber;
use app\models\Medias;
use app\models\Item;
use app\models\Models;
use app\models\ModelsPicture;
use yii\filters\AccessControl;
use app\components\AccessRule;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use app\models\UserHasCustomer;
use yii\helpers\ArrayHelper;
use app\vendor\PHelper;

class DefaultController extends Controller
{
    const FILE_UPLOAD_PATH_MODEL = "/public/images/models/";
    const FILE_UPLOAD_PATH = "/public/images/";
	
    public function behaviors()
    {
        return [
	    	'access' => [
	    		'class' => AccessControl::className(),
			    	// We will override the default rule config with the new AccessRule class
			    	'ruleConfig' => [
	    				'class' => AccessRule::className(),
	    			],
	    		'only' => ['index','create', 'update', 'view', 'delete', 'load', 'search', 'getdeleted', 'softdelete', 'revert'],
		    	'rules' => [
		    		[
		    			'actions' => ['index','create', 'update', 'view', 'delete', 'load', 'search', 'getdeleted', 'softdelete', 'revert'],
		    			'allow' => true,
		    			// Allow few users
		    			'roles' => [
		    				User::TYPE_ADMIN,
		    				User::TYPE_CUSTOMER_ADMIN,
							User::TYPE_CUSTOMER,
							User::REPRESENTATIVE,
							User::TYPE_SALES,
                            User::TYPE_SHIPPING,
                            User::TYPE_BILLING
		    			],
		    		]
		    	],
	    	]
        ];
    }

    /**
     * Lists all Inventory models.
     * @return mixed
     */
    public function actionIndex()
    {

        //$query = Inventory::find()->all();

        /*$dataProvider = new ActiveDataProvider([
            'query' => Inventory::find(),
            'pagination' => ['pageSize' => 10],
        ]);*/


       // $searchModel = new InventorySearch();
        //$dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $basket_count = Models::find()->where(['deleted' => 1])->count();
        return $this->render('index', [
            'basket_count' => $basket_count
           // 'searchModel' => $searchModel,
           // 'dataProvider' => $dataProvider,
        ]);
    }
    
    /**
     * This function is used to Soft Delete inventory.
     * @param integer $id
     */
    public function actionSoftdelete($id){
        $model = Models::findOne($id);
        $model->deleted = 1;
        if($model->save()){
                $_message = 'Inventory has been deleted successfully.';
                Yii::$app->getSession()->setFlash('danger', $_message);
        } else{
                $errors = json_encode($_model->errors) . '<br/>' . json_encode($model->errors);
                $_message = '<div class="alert alert-danger fade in"><strong>Failed!</strong>' . json_encode($errors) . '</div>';
                Yii::$app->getSession()->setFlash('error', $_message);        		
        }
        return $this->redirect(['/inventory/index']);
    }
    
    /**
     * This function is used to Revert deleted inventory.
     * @param integer $id
     */
    public function actionRevert($id){
        $model = Models::findOne($id);
        $model->deleted = 0;
        $isSuccess = $model->save();
        if($isSuccess){
                $_message = 'Inventory has been reverted successfully.';
                Yii::$app->getSession()->setFlash('success', $_message);
        } else {
                $errors = json_encode($_model->errors) . '<br/>' . json_encode($model->errors);
                $_message = '<div class="alert alert-danger fade in"><strong>Failed!</strong>' . json_encode($errors) . '</div>';
                Yii::$app->getSession()->setFlash('error', $_message);        		
        }
        return $isSuccess;
    }
    public function actionLoad()
    {

    	if (Yii::$app->request->isAjax) {
			if(Yii::$app->user->identity->usertype != User::TYPE_CUSTOMER && Yii::$app->user->identity->usertype != User::REPRESENTATIVE) {
				//$query = Inventory::find();
				$sql = "SELECT lv_manufacturers.name, lv_models.descrip, lv_models.image_id, 
						lv_models.assembly, 
						lv_models.aei, 
						lv_models.frupartnum, 
						lv_models.manpartnum, 
						lv_departements.name as department,
						lv_models.id,
                                                lv_models.category_id,
						lv_medias.filename,
						IFNULL(p.nb_models, 0) AS nb_models,
						IFNULL(p.instock_qty, 0) AS instock_qty
						FROM lv_models 
						LEFT JOIN lv_manufacturers ON lv_models.manufacturer = lv_manufacturers.id 
						LEFT JOIN lv_departements ON lv_models.department = lv_departements.id 
						LEFT JOIN lv_medias ON lv_models.image_id = lv_medias.id 
						LEFT JOIN (
							SELECT model, count(id) AS nb_models, SUM(IF(status='". array_search('In Stock', Item::$status)."',1,0)) AS instock_qty
							FROM lv_items
							WHERE status IN (". array_search('In Stock', Item::$status).")
							GROUP BY model
						) p ON (lv_models.id = p.model) where lv_models.deleted = 0
						ORDER BY lv_manufacturers.name, lv_models.descrip
						";	
			}
			else {
				$customers = ArrayHelper::getColumn(UserHasCustomer::find()->where(['userid'=>Yii::$app->user->id])->asArray()->all(), 'customerid');
                                if(!count($customers) ){
                                    $customers = array(-1);
                                }
                                $my_customers = "(".implode(",", array_map('intval', $customers)).")";
				$sql = "SELECT lv_manufacturers.name, lv_models.descrip, lv_models.image_id, 
					lv_models.assembly, 
					lv_models.aei, 
					lv_models.frupartnum, 
					lv_models.manpartnum, 
					lv_departements.name as department,
					lv_models.id,
                                        lv_models.category_id,
					lv_medias.filename,
						IFNULL(p.nb_models, 0) AS nb_models,
						IFNULL(p.instock_qty, 0) AS instock_qty
					FROM lv_models 
					LEFT JOIN lv_manufacturers ON lv_models.manufacturer = lv_manufacturers.id 
					LEFT JOIN lv_departements ON lv_models.department = lv_departements.id 
					LEFT JOIN lv_medias ON lv_models.image_id = lv_medias.id 
                                        INNER JOIN lv_items on lv_items.model = lv_models.id
					LEFT JOIN (
						SELECT model, count(id) AS nb_models, SUM(IF(status='". array_search('In Stock', Item::$status)."',1,0)) AS instock_qty
						FROM lv_items
						WHERE status = ". array_search('In Stock', Item::$status)." AND customer IN " . $my_customers . "
						GROUP BY model
					) p ON (lv_models.id = p.model)
					WHERE customer IN " . $my_customers . " and lv_models.deleted = 0
                                        GROUP BY lv_models.id
					ORDER BY lv_manufacturers.name, lv_models.descrip
					";				
//                                        print $sql; exit;
				/*$query = Inventory::find()
								->join('INNER JOIN', 'lv_items', 'lv_items.model =lv_models.id')
								->where(['lv_items.'=>])
								->groupBy('');*/
			}  		
			
			$connection = Yii::$app->getDb();
			
			$command = $connection->createCommand($sql);
			$count = count($command->queryAll());
			
			/*$dataProvider = new ActiveDataProvider([
			            'query' => $query,
			            'pagination' => ['pageSize' => 10],
			        ]);*/
			$dataProvider = new SqlDataProvider([
				'sql' => $sql,
				'totalCount' => $count,
				'pagination' => ['pageSize' => 15],
			]);			
    	
    		echo $this->renderPartial('_models', [
    				'dataProvider' => $dataProvider,
    				]);
    		exit();
    	} else {
    		 
    		throw new NotFoundHttpException('The requested page does not exist.');
    	}    	
    }
    
    public function actionGetdeleted()
    {

    	if (Yii::$app->request->isAjax) {
				//$query = Inventory::find();
				$sql = "SELECT lv_manufacturers.name, lv_models.descrip, lv_models.image_id, 
						lv_models.assembly, 
						lv_models.aei, 
						lv_models.frupartnum, 
						lv_models.manpartnum, 
						lv_departements.name as department,
						lv_models.id,
                                                lv_models.category_id,
						lv_medias.filename,
						IFNULL(p.nb_models, 0) AS nb_models,
						IFNULL(p.instock_qty, 0) AS instock_qty,
						IFNULL(p.inprogress_qty, 0) AS inprogress_qty,
						IFNULL(p.readytoship_qty, 0) AS readytoship_qty
						FROM lv_models 
						LEFT JOIN lv_manufacturers ON lv_models.manufacturer = lv_manufacturers.id 
						LEFT JOIN lv_departements ON lv_models.department = lv_departements.id 
						LEFT JOIN lv_medias ON lv_models.image_id = lv_medias.id 
						LEFT JOIN (
							SELECT model, count(id) AS nb_models, SUM(IF(status='". array_search('In Stock', Item::$status)."',1,0)) AS instock_qty,
							SUM(IF(status='". array_search('In Progress', Item::$status)."',1,0)) AS inprogress_qty,
							SUM(IF(status='". array_search('Ready to ship', Item::$status)."',1,0)) AS readytoship_qty
							FROM lv_items
							WHERE status IN (". array_search('In Stock', Item::$status).", ". array_search('In Progress', Item::$status).", ". array_search('Ready to ship', Item::$status).")
							GROUP BY model
						) p ON (lv_models.id = p.model) where lv_models.deleted = 1
						ORDER BY lv_manufacturers.name, lv_models.descrip
						";	
			
			$connection = Yii::$app->getDb();
			
			$command = $connection->createCommand($sql);
		
			$count = count($command->queryAll());
			
			$dataProvider = new SqlDataProvider([
				'sql' => $sql,
				'totalCount' => $count,
				'pagination' => ['pageSize' => 15],
			]);
    	
    		$html = $this->renderPartial('_deleted', [
    				'dataProvider' => $dataProvider,
    				]);
                $_retArray = array('success' => true, 'html' => $html, 'count' => $count);
		echo json_encode($_retArray);
    		exit();
    	} else {
    		 
    		throw new NotFoundHttpException('The requested page does not exist.');
    	}    	
    }
    
    
    /**
     * Displays a single Inventory model.
     * @param string $id
     * @return mixed
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new Inventory model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
		$session = Yii::$app->session;

        $model = new Models();
         
        $partnumber = array(); 
		
		$data = Yii::$app->request->post();
		
		if (!empty($data)) {	
			$successorder = false;
			$_new_media_id = 0;
			//
			$model->category_id = $data['category'];
			$model->manufacturer = $data['manufacturer'];
			$model->department = $data['department'];
			$model->descrip = $data['descrip'];
			$model->aei = $data['model_aei'];
			$model->purchasepricing = $data['purchasepricing'];
			$model->repairpricing = $data['repairpricing'];		
			$model->purchasepricingtier2 = $data['purchasepricing2'];
			$model->repairpricingtier2 = $data['repairpricing2'];
			//advanced options
			$model->frupartnum = $data['frupartnum'];
			$model->manpartnum = $data['manpartnum'];
			$model->prefered_vendor = $data['prefered_vendor'];
			$model->secondary_vendor = $data['secondary_vendor'];
			//additional options
			$model->reorderqty = $data['reorderqty'];
			$model->palletqtylimit = $data['palletqtylimit'];
			$model->stripcharacters = $data['stripcharacters']; 
			$model->checkit = $data['checkit'];
			$model->charactercount = $data['charactercount'];
			
			/*if (!isset($_POST['photo_conversion']))
				$model->photo_conversion = 0;
			else
				$model->photo_conversion = 1;*/ 
			
			if (!isset($_POST['serialized']))
				$model->serialized = 0;
			else
				$model->serialized = 1;
			
			if (!isset($_POST['preowneduseditems']))
				$model->preowneditems = 0;
			else
				$model->preowneditems = 1; 

			if (!isset($_POST['requiretesting']))
				$model->requiretestingreferb = 0;
			else
				$model->requiretestingreferb = 1; 		
			//var_dump(ModelsPicture::find()->where(['_key'=>$session['__new_model_key']])->all());exit(1);
			if($model->save()) {
				$successorder = true;
				//customer options 
				foreach($data['modelCustomer'] as $key => $value)
				{
					$partnumber = new Partnumber;
					$partnumber->customer = $data['modelCustomerval'][$key];
					$partnumber->model = $model->id;
					$partnumber->partid = $data['partid'][$key];
					$partnumber->partdescription = $data['partdesc'][$key];
					$partnumber->purchasepricing = $data['ppurchasepricing'][$key];
					$partnumber->purchasepricingtier2 = $data['ppurchasepricing2'][$key];
					$partnumber->repairpricing = $data['prepairpricing'][$key];
					$partnumber->repairpricingtier2 = $data['prepairpricing2'][$key];
					$partnumber->type = $data['parttype'][$key];
					$partnumber->save();
				}
				//save pictures modelid
				$pictures = ModelsPicture::find()->where(['_key'=>$session['__new_model_key']])->all();
				//var_dump($pictures);exit(1);
				foreach($pictures as $picture)
				{
					$picture->modelid = $model->id;
					$picture->save();
				}
				//
				
				if(!empty($pictures))
					$model->image_id = $pictures[0]->mediaid;
				else {
					$media = new Medias();
					$media->filename = "no_image.jpg";
					$media->path = self::FILE_UPLOAD_PATH_MODEL;
					$media->type = 1;
					$media->save();
					$model->image_id = $media->id;
				} 
					
				$model->save();
				//
				PHelper::generateModelsJson($model->id);
			}
			if($successorder === true){
								
				$_manufacturer = \app\models\Manufacturer::findOne($model->manufacturer);
				 
				$name = $_manufacturer->name . ' ' . $model->descrip;
								
				$_message = '<div class="alert alert-success fade in"><strong>Success!</strong> Model {<strong>'. $name .'</strong>} has been added successfully!</div>';
				
				Yii::$app->getSession()->setFlash('success', $_message);
			} else{
				$errors = json_encode($model->errors) . '<br/>' . json_encode($partnumber->errors);
				
				$_message = '<div class="alert alert-danger fade in"><strong>Failed!</strong>' . $errors . '</div>';
				
				Yii::$app->getSession()->setFlash('error', $_message);        		
			}
			
			return $this->redirect(Yii::$app->request->referrer);
		} else {
			$session['__new_model_key'] = md5(uniqid(true));
            return $this->render('create', [
                'model' => $model,
            	'partnumber' => $partnumber,
				'files' => array()
            ]);
        }
    }
	
	public function actionShowmodelsuploaded()
	{
		$html = $this->renderAjax('_loaded_models_upload');
		
		$_retArray = array('success' => true, 'html' => $html);
		echo json_encode($_retArray);
		exit();		
	}
	
	public function actionRemovemodeluploaded()
	{
		$session = Yii::$app->session;
		
		$_post = Yii::$app->request->get();
		
		$_find_1 = Medias::find()->innerJoin('lv_models_pictures', '`lv_models_pictures`.`mediaid` = `lv_medias`.`id`')
						->where(['like', 'filename', $_post['file']])
						->andWhere(['_key' => $session['__new_model_key']])
						->groupBy('`lv_models_pictures`.`mediaid`')
						->one();
		//var_dump($_find_1, $_post['file']);exit(1);
		if($_find_1!==null) {			
			$_find_2 = ModelsPicture::find()->where(['mediaid'=>$_find_1->id])->one();
			$_find_1->delete();
			$_find_2->delete();
		}			
	}
    
    public function actionSearch()
    {
    	$_post = Yii::$app->request->get();
    	
    	//if (Yii::$app->request->isAjax) {
    		$_retArray = array('success' => FALSE, 'html' => '');
    		if (!isset($_post['query'])) {
    			$_retArray = array('success' => FALSE, 'html' => 'Something is wrong! Plese try again!');
    			echo json_encode($_retArray);
    			exit();
    		}
    		
    		$query = $_post['query']; 
    		
	    	$searchModel = new InventorySearch();
	    	$dataProvider = $searchModel->search(['InventorySearch'=>['descrip'=>$query]]);
	    
	    	$html = $this->renderPartial('_smodels', [
	    			'dataProvider' => $dataProvider,
	    			]);
	    	
	    	$_retArray = array('success' => true, 'html' => $html, 'count'=>$dataProvider->getTotalCount());
	    	echo json_encode($_retArray);
	    	exit();
    	/*} else { 
    	
    		throw new NotFoundHttpException('The requested page does not exist.');
    	}*/
    }

    /**
     * Updates an existing Inventory model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param string $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
		
		$session = Yii::$app->session;
        
        $partnumbers = Partnumber::find()->where(['model'=>$model->id])->all();
        
        $media = Medias::findOne($model->image_id);
        
       // var_dump($partnumber);

    		$data = Yii::$app->request->post();
		
		if (!empty($data)) {	
			$successorder = false;
			
			$model->category_id = $data['category'];
			$model->manufacturer = $data['manufacturer'];
			$model->department = $data['department'];
			$model->descrip = $data['descrip'];
			$model->aei = $data['model_aei'];
			$model->purchasepricing = $data['purchasepricing'];
			$model->repairpricing = $data['repairpricing'];
			$model->purchasepricingtier2 = $data['purchasepricing2'];
			$model->repairpricingtier2 = $data['repairpricing2'];
			//advanced options
			$model->frupartnum = $data['frupartnum'];
			$model->manpartnum = $data['manpartnum'];
			$model->prefered_vendor = $data['prefered_vendor'];
			$model->secondary_vendor = $data['secondary_vendor'];
			//additional options
			$model->reorderqty = $data['reorderqty'];
			$model->palletqtylimit = $data['palletqtylimit'];
			$model->stripcharacters = $data['stripcharacters'];
			$model->checkit = $data['checkit'];
			$model->charactercount = $data['charactercount'];

			if (!isset($_POST['serialized']))
				$model->serialized = 0;
			else
				$model->serialized = 1;
				
			if (!isset($_POST['preowneduseditems']))
				$model->preowneditems = 0;
			else
				$model->preowneditems = 1;
			
			if (!isset($_POST['requiretesting']))
				$model->requiretestingreferb = 0;
			else
				$model->requiretestingreferb = 1;
				
			if($model->save())
			{
				$successorder = true;	

				foreach ($partnumbers as $partnumber)
				{
					$partnumber->delete();
				}				
				
				foreach($data['modelCustomer'] as $key => $value)
				{
					//$partnumber  = Partnumber::find()->where(['customer'=>$data['modelCustomerval'][$key], 'model'=>$model->id])->one();
					
					//if(empty($partnumber))
					$partnumber = new Partnumber;
					//var_dump($data['modelCustomerval'][$key], $model->id, $partnumber);exit(1);
					$partnumber->customer = $data['modelCustomerval'][$key];
					$partnumber->model = $model->id;
					$partnumber->partid = $data['partid'][$key];
					$partnumber->partdescription = $data['partdesc'][$key];
					$partnumber->purchasepricing = $data['ppurchasepricing'][$key];
					$partnumber->purchasepricingtier2 = $data['ppurchasepricing2'][$key];
					$partnumber->repairpricing = $data['prepairpricing'][$key];
					$partnumber->repairpricingtier2 = $data['prepairpricing2'][$key];
					$partnumber->type = $data['parttype'][$key];
					$partnumber->save();
				}		
				
				//save pictures modelid
				$pictures = ModelsPicture::find()->where(['_key'=>$session['__new_model_key']])->andWhere(['modelid'=>null])->all();
				
				foreach($pictures as $picture)
				{
					$picture->modelid = $model->id;
					$picture->save();
				}				
				
				if(!empty($pictures))
					$model->image_id = $pictures[0]->mediaid;
				$model->save();
				//
				PHelper::updateModelsJson($model->id);
			}
			if($successorder === true){
				
				$_manufacturer = \app\models\Manufacturer::findOne($model->manufacturer);
					
				$name = $_manufacturer->name . ' ' . $model->descrip;
								
				$_message = '<div class="alert alert-warning fade in"><strong>Success!</strong> Model {<strong>'. $name .'</strong>} has been updated successfully!</div>';
				
				Yii::$app->getSession()->setFlash('success', $_message);
			} else{
				$errors = $model->errors . '<br/>' . $item->errors . '<br/>' . $order->errors;
				$_message = '<div class="alert alert-danger fade in"><strong>Failed!</strong>' . json_encode($errors) . '</div>';
				Yii::$app->getSession()->setFlash('error', $_message);        		
			}
			
			return $this->redirect(Yii::$app->request->referrer);

        } else {
			
			$session['__new_model_key'] = ModelsPicture::find()->where(['modelid'=>$model->id])->one()->_key;
			if(empty($session['__new_model_key']))
				$session['__new_model_key'] = md5(uniqid(true));
			
            return $this->render('update', [
                'model' => $model,
            	'partnumber' => $partnumbers, 
            	'media' => $media,
				'files' => ModelsPicture::find()->where(['modelid'=>$model->id])->all()
            ]);
        }
    }

    /**
     * Deletes an existing Inventory model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param string $id
     * @return mixed
     */
    public function actionDelete($id)
    {
    	PHelper::deleteModelsJson($id);
    	
    	$model = \app\models\Models::findOne($id);
    		
    	$_manufacturer = \app\models\Manufacturer::findOne($model->manufacturer);
    	
    	$name = $_manufacturer->name . ' ' . $model->descrip;
    	
        if($this->findModel($id)->delete())
        {
        	$_message = '<div class="alert alert-danger fade in">Model {<strong>'. $name .'</strong>} has been successfully deleted !</div>';
        	
        	Yii::$app->getSession()->setFlash('error', $_message);        	
        }

        return $this->redirect(['index']);
    }

    /**
     * Finds the Inventory model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $id
     * @return Inventory the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Models::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
	
    /**
     * 
     * @param type $_upoload_file
     * @param type $_type
     * @return boolean|string
     */
    private function uploadMedia($_upoload_file, $_type = '') {

        /***
         * to create folders if not folders are not available
         */
        
        $new_path = Yii::getAlias('@webroot') . self::FILE_UPLOAD_PATH;
        if (!is_dir($new_path)) {
            mkdir($new_path, 0777, true);
        }

        
        $new_path = Yii::getAlias('@webroot') . self::FILE_UPLOAD_PATH_MODEL;
        if (!is_dir($new_path)) {
            mkdir($new_path, 0777, true);
        }

        
        $_error = "";
        $target_dir = Yii::getAlias('@webroot') . self::FILE_UPLOAD_PATH_MODEL;
        $target_file = $target_dir . basename($_upoload_file["name"]);
        $uploadOk = 1;
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
        if ($_type == "image") {
            $check = getimagesize($_upoload_file["tmp_name"]);
            if ($check !== false) {
                $uploadOk = 1;
            } else {
                return $_error = $_error . "File is not an image.";
            }
        }

        if ($_upoload_file["size"] > 2000000) {
            return $_error = $_error . "Sorry, your file is too large.";
        }
        if ($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif") {
            return $_error = $_error . "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
        }
        if (1) {

            $temp = explode(".", $_upoload_file["name"]);
            $newfilename = round(microtime(true)) . '.' . end($temp);
            if (move_uploaded_file($_upoload_file["tmp_name"], $target_dir . $newfilename)) {

                $_success['success'] = true;
                $_success['filename'] = $newfilename;
                return $_success;
            } else {

                return $_error = $_error . "Sorry, there was an error uploading your file.";
            }
        }
        return false;
    }
}