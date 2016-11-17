<?php

namespace app\modules\Vendor\controllers;

use Yii;
use app\models\Vendor;
use app\models\User;
use app\models\VendorSearch;
use app\components\AccessRule;
use yii\filters\AccessControl;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

class DefaultController extends Controller
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
				'only' => ['index','create', 'update', 'view', 'delete'],
				'rules' => [
					[
						'actions' => ['index','create', 'update', 'view', 'delete'],
						'allow' => true,
						// Allow few users
						'roles' => [
							User::TYPE_ADMIN,
							User::TYPE_CUSTOMER_ADMIN,
							User::TYPE_BILLING,
							User::TYPE_SALES,
							User::TYPE_CUSTOMER
						],
					]
				],
			]
		];
	}
	
    /**
     * Lists all Vendor models.
     * @return mixed
     */
    public function actionIndex()
    {
        $dataProvider = new ActiveDataProvider([
            'query' => Vendor::find(),
        	'pagination' => ['pageSize' => 10],
        	'sort'=> ['defaultOrder' => ['vendorid'=>SORT_ASC]]
        ]);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
        ]);
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
	    	 
	    	$searchModel = new VendorSearch();
	    	$dataProvider = $searchModel->search(['VendorSearch'=>['vendorid'=>trim($query)]]);
	    	 
	    	$html = $this->renderPartial('_vendor', [
	    			'dataProvider' => $dataProvider,
	    			]);
	    	 
	    	$_retArray = array('success' => true, 'html' => $html, 'count'=>$dataProvider->getTotalCount());

	    	Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
	    	//return view
	    	return $_retArray;
	    	exit();
    	/*} else {
    	  
    	throw new NotFoundHttpException('The requested page does not exist.');
    	}*/
    }

    /**
     * Displays a single Vendor model.
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
     * Creates a new Vendor model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Vendor();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing Vendor model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing Vendor model.
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
     * Finds the Vendor model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Vendor the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Vendor::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
