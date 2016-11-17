<?php

namespace app\controllers;

use Yii;
use app\models\Category;
use app\models\Models;
use app\models\Item;
use yii\data\ActiveDataProvider;
use yii\data\SqlDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use app\models\UserHasCustomer;
use yii\helpers\ArrayHelper;
use yii\filters\AccessControl;
use app\components\AccessRule;
use app\models\User;

/**
 * CategoriesController implements the CRUD actions for Category model.
 */
class CategoryController extends Controller
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
				'only' => ['index','create', 'update', 'view', 'delete', 'load', 'loadmodels'],
				'rules' => [
					[
						'actions' => ['index', 'create', 'update', 'view', 'delete', 'load', 'loadmodels'],
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
     * Lists all Category models.
     * @return mixed
     */
    public function actionIndex()
    {
        $dataProvider = new ActiveDataProvider([
            'query' => Category::find(),
        ]);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
        ]);
    } 
    
    public function actionLoad()
    {
    
    	if (Yii::$app->request->isAjax) {
    		
    		$query = Category::find()->select('lv_categories.id, categoryname')
    					->join('INNER JOIN', 'lv_models', 'lv_models.category_id =lv_categories.id')
    					->groupBy('category_id');
    		 
    		$dataProvider = new ActiveDataProvider([
    					'query' => $query,
    					'pagination' => ['pageSize' => 15],
    				]);
    		 
    		echo $this->renderAjax('_load', [
    				'dataProvider' => $dataProvider,
    				]);
    		exit();
    	} else {
    		 
    		throw new NotFoundHttpException('The requested page does not exist.');
    	}
    }
    
    public function actionLoadmodels()
    {
    	//if (Yii::$app->request->isAjax) {
    		
    		$data = Yii::$app->request->get();
			
			if(Yii::$app->user->identity->usertype != 1) {
				//$query = Models::find()->where([''=>]);
				$sql = "SELECT lv_manufacturers.name, lv_models.descrip, lv_models.image_id, 
					lv_models.assembly, 
					lv_models.aei, 
					lv_models.frupartnum, 
					lv_models.manpartnum, 
					lv_departements.name as department,
					lv_models.id,
					lv_medias.filename,
						IFNULL(p.nb_models, 0) AS nb_models,
						IFNULL(p.instock_qty, 0) AS instock_qty,
						IFNULL(p.inprogress_qty, 0) AS inprogress_qty,
						IFNULL(p.readytoship_qty, 0) AS readytoship_qty
					FROM lv_models 
					LEFT JOIN lv_manufacturers ON lv_models.manufacturer = lv_manufacturers.id 
					LEFT JOIN lv_departements ON lv_models.department = lv_departements.id 
					LEFT JOIN lv_medias ON lv_models.image_id = lv_medias.id 
					INNER JOIN (
						SELECT model, count(id) AS nb_models, SUM(IF(status='". array_search('In Stock', Item::$status)."',1,0)) AS instock_qty,
						SUM(IF(status='". array_search('In Progress', Item::$status)."',1,0)) AS inprogress_qty,
						SUM(IF(status='". array_search('Ready to ship', Item::$status)."',1,0)) AS readytoship_qty
						FROM lv_items
						GROUP BY model
					) p ON (lv_models.id = p.model)
					WHERE category_id = '" . $data['idcategory'] . "'
					ORDER BY name, descrip
					";				
			} else {
				$customers = ArrayHelper::getColumn(UserHasCustomer::find()->where(['userid'=>Yii::$app->user->id])->asArray()->all(), 'customerid');			
				/*$query = Models::find()
							->join('INNER JOIN', 'lv_items', 'lv_items.model =lv_models.id')
							->where(['category_id'=>$data['idcategory']])
							->andWhere(['lv_items.customer'=>$customers])
							->groupBy('lv_items.model');*/
				$sql = "SELECT lv_manufacturers.name, lv_models.descrip, lv_models.image_id, 
					lv_models.assembly, 
					lv_models.aei, 
					lv_models.frupartnum, 
					lv_models.manpartnum, 
					lv_departements.name as department,
					lv_models.id,
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
					) p ON (lv_models.id = p.model)
					WHERE customer = '" . implode(',', $customers) . "'
					AND category_id = '" . $data['idcategory'] . "'
					ORDER BY id
					";	
			}
    		 
    		$dataProvider = new SqlDataProvider([
    				'sql' => $sql,
    				'pagination' => ['pageSize' => 15],
    				]);
    		 
    		echo $this->renderPartial('@app/modules/Inventory/views/default/_models', [
    				'dataProvider' => $dataProvider,
    				]);
    		exit();
    	/*} else {
    		 
    		throw new NotFoundHttpException('The requested page does not exist.');
    	}*/
    }

    /**
     * Displays a single Category model.
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
     * Creates a new Category model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Category();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing Category model.
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
     * Deletes an existing Category model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        if($this->request->isPostRequest){ 
        $this->findModel($id)->delete();
        }

        return $this->redirect(['index']);
    }

    /**
     * Finds the Category model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Category the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Category::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
