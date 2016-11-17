<?php

namespace app\controllers;

use Yii;
use app\models\Location;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use app\components\AccessRule;
use app\models\User;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;

/**
 * LocationController implements the CRUD actions for Location model.
 */
class LocationController extends Controller
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
							User::TYPE_CUSTOMER,
                            User::TYPE_SHIPPING
		    			],
		    		]
		    	],
	    	],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['post'],
                ],
            ],
        ];
    }

    /**
     * Lists all Location models.
     * @return mixed
     */
    public function actionIndex()
    {
        $dataProvider = new ActiveDataProvider([
            'query' => Location::find(),
        ]);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Location model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id, $order=null)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
			'order'=>$order
        ]);
    }

    /**
     * Creates a new Location model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Location();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing Location model.
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
     * Deletes an existing Location model.
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
     * Finds the Location model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Location the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Location::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
