<?php

namespace app\controllers;

use Yii;
use app\models\ModelOption;
use app\models\Customer;
use app\models\ItemHasOption;
use yii\helpers\ArrayHelper;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * ModeloptionController implements the CRUD actions for ModelOption model.
 */
class ModeloptionController extends Controller
{
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['post'],
                ],
            ],
        ];
    }

    /**
     * Lists all ModelOption models.
     * @return mixed
     */
    public function actionIndex()
    {
        $dataProvider = new ActiveDataProvider([
            'query' => ModelOption::find(),
        ]);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single ModelOption model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
    	if (Yii::$app->request->isAjax) {
    	
    		$_retArray = array('success' => FALSE, 'html' => '');
    		$_post = Yii::$app->request->get();
    		if (!isset($_post['id'])) {
    			$_retArray = array('success' => FALSE, 'html' => 'Something is wrong! Plese try again!');
    			echo json_encode($_retArray);
    			exit();
    		}
    		$id = $_post['id'];
    		$find = ModelOption::find()->where(['optiontype'=>2, 'parent_id'=>0, 'idmodel'=>$id])->all();
    		$html = $this->renderAjax('view', [
    				'models' => $find
    				]);
    		$_retArray = array('success' => true, 'html' => $html);
    		echo json_encode($_retArray);
    		exit();
    	} else {
    	
    		throw new NotFoundHttpException('The requested page does not exist.');
    	}
    	//$model = $this->findModel($id);
    }
    
    public function actionAjaxcreate()
    {
    	if (Yii::$app->request->isAjax) {
    		
    		$_retArray = array('success' => FALSE, 'html' => '');
    		$_post = Yii::$app->request->get();
    		
    		if (!isset($_post['itemid'])) {
    			$_retArray = array('success' => FALSE, 'html' => 'Something is wrong! Plese try again!');
    			echo json_encode($_retArray);
    			exit();
    		}
    		
    		$id = $_post['itemid'];
    		
	    	$model = ModelOption::find()->where(['optiontype'=>2, 'parent_id'=>0, 'idmodel'=>$id])->all();
	    
	    	$thml = $this->renderAjax('ajax_create', [
	    			'model' => $model,
	    			]);
	    	$_retArray = array('success' => true, 'html' => $html);
	    	echo json_encode($_retArray);
	    	exit();
    	} else {
    	
    		throw new NotFoundHttpException('The requested page does not exist.');
    	}
    }

    /**
     * Creates a new ModelOption model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
    	if (Yii::$app->request->isAjax) {
    	
    		$_retArray = array('success' => FALSE, 'html' => '');
    		//$_post = Yii::$app->request->get();
    	
	        $model = new ModelOption();
	        
            $html = $this->renderAjax('_form', [
                'model' => $model,
            ]);
            
            $_retArray = array('success' => true, 'html' => $html);
            echo json_encode($_retArray);
            exit();
            
        } else {
        	throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    /**
     * Updates an existing ModelOption model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id //option id
     * @param integer $idmodel // model id
     * @return mixedcreateoption
     */
    public function actionCreateoption()
    {
    	//var_dump($_POST);
    	$name = $_POST['name'];
    	$options = $_POST['options'];
    	//$model = 
    	
    	$model_1 = new ModelOption;
    	$model_1->idmodel = $_POST['idmodel'];
    	$model_1->name = $_POST['name'];
    	$model_1->optiontype = 2;
    	$model_1->level = 1;
    	$model_1->parent_id = 0;
    	$model_1->checkable = 0;
    	$model_1->save();
    	
    	foreach($options as $option)
    	{
    		$model_2 = new ModelOption;
    		$model_2->idmodel = $_POST['idmodel'];
    		$model_2->name = $option;
    		$model_2->optiontype = 2;
    		$model_2->level = 1;
    		$model_2->parent_id = $model_1->id;
    		$model_2->checkable = 1;
    		$model_2->save();		
    	}
    	
    	$_message = '<div class="alert alert-success"><strong>Success!</strong> Option has been created successfully!</div>';
    	Yii::$app->getSession()->setFlash('success', $_message);
    	return $this->redirect(Yii::$app->request->referrer);
    }
    
    public function actionLoadorderoption()
    {
    	if (Yii::$app->request->isAjax) {
    		 
    		$_retArray = array('success' => FALSE, 'html' => '');
    		$_post = Yii::$app->request->get();
    		if (!isset($_post['id'])) {
    			$_retArray = array('success' => FALSE, 'html' => 'Something is wrong! Plese try again!');
    			echo json_encode($_retArray);
    			exit();
    		}
    		$id = $_post['id'];
    		$entry_no = $_post['entry_no'];
    		$find = ModelOption::find()->where(['optiontype'=>2, 'parent_id'=>0, 'idmodel'=>$id])->all();
    		$html = $this->renderAjax('_orderoption', [
    				'models' => $find,
    				'entry_no' => $entry_no
    				]);
    		if(!$find)
    			$html = "";
    		$_retArray = array('success' => true, 'html' => $html);
    		echo json_encode($_retArray);
    		exit();
    	} else {
    		 
    		throw new NotFoundHttpException('The requested page does not exist.');
    	}    	
    }
	
	public function actionLoadpurchaseorderoption($ordertype=1, $customerid=null)
	{
    	if (Yii::$app->request->isAjax) {
    		 
    		$_retArray = array('success' => FALSE, 'html' => '');
    		$_post = Yii::$app->request->get();
    		if (!isset($_post['id'])) {
    			$_retArray = array('success' => FALSE, 'html' => 'Something is wrong! Plese try again!');
    			echo json_encode($_retArray);
    			exit();
    		}
    		$id = $_post['id'];
    		$entry_no = $_post['entry_no'];
    		$find = ModelOption::find()->where(['optiontype'=>2, 'parent_id'=>0, 'idmodel'=>$id])->all();
			//
			$customer = array();
			$_findlastorderoption = array();
			//
			if(!empty($customerid)) {
				$customer = Customer::findOne($customerid);
				$_findlastorderoption = ItemHasOption::find()
												->innerJoin('lv_salesorders', '`lv_salesorders`.`id` = `lv_item_has_option`.`orderid`')
												->where(['customer_id'=>$customerid, 'itemid'=>$id])
												->orderBy('`lv_item_has_option`.`id` DESC')
												->one();
				$_findlastorderoptions = ArrayHelper::getColumn(ItemHasOption::find()
												->innerJoin('lv_salesorders', '`lv_salesorders`.`id` = `lv_item_has_option`.`orderid`')
												->where(['customer_id'=>$customerid, 'itemid'=>$id, 'orderid'=>$_findlastorderoption->orderid])
												->distinct()->asArray()->all(), 'optionid');												
			}
			//if(empty($optionid)) {
				$html = $this->renderAjax('_orderoption', [
							'models' => $find,
							'entry_no' => $entry_no,
							'ordertype' => $ordertype,
							'customer' => $customer,
							'_findlastorderoption' => $_findlastorderoption,
							'_findlastorderoptions' => $_findlastorderoptions,
						]);
			/*} else {
				//$option = ModelOption::findOne($optionid);
				$html = $this->renderAjax('_orderoption', [
							'models' => $find,
							'entry_no' => $entry_no,
							'ordertype' => $ordertype,
							'option' => $option
						]);				
			}*/
    		if(!$find)
    			$html = "";
    		$_retArray = array('success' => true, 'html' => $html);
    		echo json_encode($_retArray);
    		exit();
    	} else {
    		 
    		throw new NotFoundHttpException('The requested page does not exist.');
    	}   		
	}
    
    public function actionUpdate()
    {
    	
    	if (Yii::$app->request->isAjax) {
    		 
    		$_retArray = array('success' => FALSE, 'html' => '');
    		$_post = Yii::$app->request->get();
    		if (!isset($_post['id'])) {
    			$_retArray = array('success' => FALSE, 'html' => 'Something is wrong! Plese try again!');
    			echo json_encode($_retArray);
    			exit();
    		}
    		$id = $_post['id'];
    		$idmodel = $_post['idmodel'];
    		$type = $_post['type'];
    		if($type=='sub')
    			$find = ModelOption::find()->where(['optiontype'=>2, 'parent_id'=>$id, 'checkable'=>0])->all();
    		else 
    			$find = ModelOption::find()->where(['optiontype'=>2, 'parent_id'=>$id, 'checkable'=>1])->all();
    		$model = $this->findModel($id);
    		
    		$html = $this->renderAjax('update', [
    					'models' => $find,
    					'model' => $model
    				]);
    		$_retArray = array('success' => true, 'html' => $html);
    		echo json_encode($_retArray);
    		exit();
    	} else {
    		 
    		throw new NotFoundHttpException('The requested page does not exist.');
    	}
    }

    /**
     * Deletes an existing ModelOption model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the ModelOption model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return ModelOption the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = ModelOption::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
