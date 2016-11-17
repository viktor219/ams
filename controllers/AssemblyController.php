<?php

namespace app\controllers;

use Yii;
use app\models\ModelAssembly;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use app\models\User;
use app\models\Models;
use app\models\Partnumber;
use yii\filters\AccessControl;
use app\components\AccessRule;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use app\models\UserHasCustomer;
use yii\helpers\ArrayHelper;

/**
 * ModelAssemblyController implements the CRUD actions for ModelAssembly model.
 */
class AssemblyController extends Controller
{
	public function behaviors()
	{
		return [
			'access' => [
				'class' => AccessControl::className(),
				// We will override the default rule config with the new AccessRule class
				'ruleConfig' => [
					'class' => AccessRule::className(),
				],
				'only' => ['index','create', 'update', 'view', 'delete', 'load'],
				'rules' => [
					[
						'actions' => ['index', 'create', 'update', 'view', 'delete', 'load'],
						'allow' => true,
						// Allow few users
						'roles' => [
							User::TYPE_ADMIN,
							User::TYPE_CUSTOMER_ADMIN,
							User::TYPE_CUSTOMER,
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
     * Lists all ModelAssembly models.
     * @return mixed
     */
    public function actionIndex()
    {
        $dataProvider = new ActiveDataProvider([
            'query' => ModelAssembly::find()->groupBy('modelid'),
        ]);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single ModelAssembly model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new ModelAssembly model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new ModelAssembly();
        
        $data = Yii::$app->request->post();

		$errors = null;

        if (!empty($data)) {
            //var_dump($data);
            //save models before
        	$_model = new Models();
        	$_model->descrip = $data['assembly_name'];
			$_model->assembly = 1;
        	if($_model->save()) {
	            foreach($data['quantity'] as $key=>$value)
	            {
	            	if(!empty($data['description'][$key]))
	            	{
	            		//for($i=0; $i<$value;$i++) {
		            		$model = new ModelAssembly();
		            		$model->customerid = $data['customerId'];
		            		$model->modelid = $_model->id;
		            		$model->partid = $data['partid'][$key];
		            		$model->quantity = $data['quantity'][$key];
							if($model->save())
								$successorder = true;
							else {
								$successorder = false;
								$errors .= '<br/>' . $model->errors;
							}
							//
							if(isset($data['customerId']) && $data['partnumber'][$key]!=="")
							{
								//echo 'here';exit(1);
								$_find_partnumber = Partnumber::find()->where(['customer'=>$data['customerId'], 'model'=>$_model->id])->one();
								if($_find_partnumber === null) {
									$partnumber = new Partnumber;
									$partnumber->customer = $data['customerId'];
									$partnumber->partid = $data['partnumber'][$key];
								} else 
									$partnumber = $_find_partnumber;
								//var_dump($partnumber);exit(1);
								$partnumber->partdescription = $data['partnumber'][$key];
								$partnumber->model = $_model->id;
								$partnumber->save();
							}
	            		//}
	            	}
	            }
        	}else {
				$successorder = false;
				$errors .= '<br/>' . $_model->errors;
			}
			//
        	if($successorder === true){
        		$_message = '<div class="alert alert-success"><strong>Success!</strong> Assembly {' . $data['assembly_name'] . '} has been created successfully!</div>';
        		Yii::$app->getSession()->setFlash('success', $_message);
        	} else{
        		$_message = '<div class="alert alert-danger"><strong>Failed!</strong>' . json_encode($errors) . '</div>';
        		Yii::$app->getSession()->setFlash('error', $_message);        		
        	}

			return $this->redirect(Yii::$app->request->referrer);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }
    
    public function actionLoad()
    {
    
    	if (Yii::$app->request->isAjax) {
			if(Yii::$app->user->identity->usertype != 1)
				$query = ModelAssembly::find()->groupBy('modelid');
			else {
				$customers = ArrayHelper::getColumn(UserHasCustomer::find()->where(['userid'=>Yii::$app->user->id])->asArray()->all(), 'customerid');
				$query = ModelAssembly::find()
							->where(['customerid'=>$customers])
							->groupBy('modelid');
			}   		 
    		$dataProvider = new ActiveDataProvider([
    				'query' => $query,
    				'pagination' => ['pageSize' => 10],
    				]);
    		 
    		echo $this->renderPartial('_load', [
    				'dataProvider' => $dataProvider,
    				]);
    		exit();
    	} else {
    		 
    		throw new NotFoundHttpException('The requested page does not exist.');
    	}
    }    

    /**
     * Updates an existing ModelAssembly model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = Models::findOne($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('update', [
                'model' => $model,
				'assembly' => ModelAssembly::findOne(['modelid'=>$model->id]),
				'assemblies' => ModelAssembly::findAll(['modelid'=>$model->id])
            ]);
        }
    }

    /**
     * Deletes an existing ModelAssembly model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete()
    {
    	$_post = Yii::$app->request->get();
    	if (Yii::$app->request->isAjax) {
    		$_retArray = array('success' => FALSE, 'html' => '');
    		if (!isset($_post['id'])) {
    			$_retArray = array('success' => FALSE, 'html' => 'Something is wrong! Plese try again!');
    			echo json_encode($_retArray);
    			exit();
    		}
	    	$id = $_post['id'];

			$models = ModelAssembly::find()->where(['modelid'=>$id])->all();

	    	$assembly = Models::findOne($id);

			$assembly_name = $assembly->descrip;

			$assembly->delete();
	    	
	    	foreach ($models as $model)
	    	{
	        	$this->findModel($model->id)->delete();
	    	}
	    	
	    	$_retArray = array('success' => true, 'html' => "Assmebly { $assembly_name } has been successfully removed");
	    	
	    	echo json_encode($_retArray);
	    	exit();
    	} else {    	
    		throw new NotFoundHttpException('The requested page does not exist.');
    	}    	
    }

    /**
     * Finds the ModelAssembly model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return ModelAssembly the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = ModelAssembly::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
