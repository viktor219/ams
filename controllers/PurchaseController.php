<?php

namespace app\controllers;

use Yii;
use app\models\Purchase;
use app\models\Item;
use app\models\PurchaseSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\data\ActiveDataProvider;
use yii\filters\VerbFilter;

/**
 * PurchaseController implements the CRUD actions for Purchase model.
 */
class PurchaseController extends Controller
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
     * Lists all Purchase models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new PurchaseSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Purchase model.
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
     * Creates a new Purchase model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Purchase();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }
    
        /**
        * Revert deletion of an existing Purchasing model.
        * @param integer $id
        * @return boolean
        */
        public function actionRevertitems($id){
            $model = Item::findOne($id);
			
			$items = Item::find()->where(['status'=>array_search('Requested', Item::$status), 'ordernumber'=>$model->ordernumber, 'model'=>$model->model])->all();
				
			//echo count($items);
				
			foreach($items as $item)
			{
				$item->deleted = 0;
				$item->save();
			}
			
			return true;
        }
        
        /**
        * Revert deletion of an existing Purchasing model.
        * @param integer $id
        * @return boolean
        */
        public function actionRevert($id){
            $model = Purchase::findOne($id);
            $model->deleted = 0;            
            return $model->save(false);
        }

    /**
     * Updates an existing Purchase model.
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
     * Deletes an existing Purchase model.
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
     * Soft delete the existing Items Purchased item.
     * @param $id integer
     * @return mixed
     */    
    public function actionSdelete($id){
        $model = Purchase::findOne($id);
        $model->deleted = 1;
        if($model->save(false)){
                $_message = 'P0#: ' . $model->number_generated . ' has been deleted successfully!';
                Yii::$app->getSession()->setFlash('danger', $_message);      
            } else {
                $_message = 'There is a problem in deleting User!';
                Yii::$app->getSession()->setFlash('warning', $_message);     
            }
        return $this->redirect(['/purchasing/index']);
    }
    
        /**
        * Get deleted Customers
        * @return mixed
        */
        public function actionGetdeleted(){
            if(Yii::$app->user->identity->usertype != 1){
                $deletedItemsRequested = Item::find()
                        ->innerJoin('lv_salesorders', '`lv_salesorders`.`id` = `lv_items`.`ordernumber`')
                        ->where(['status'=>1, 'purchaseordernumber'=>NULL])
                        ->andWhere('lv_salesorders.ordertype IN (1,2,3,4)')
                        ->andWhere('lv_items.deleted  = 1')
                        ->groupBy('model, ordernumber');
            } else {
                $customers = ArrayHelper::getColumn(UserHasCustomer::find()->where(['userid'=>Yii::$app->user->id])->asArray()->all(), 'customerid');						
                $deletedItemsRequested = Item::find()
                        ->innerJoin('lv_salesorders', '`lv_salesorders`.`id` = `lv_items`.`ordernumber`')
                        ->where(['ordertype'=>2, 'status'=>1, 'purchaseordernumber'=>NULL])
                        ->andWhere(['`lv_salesorders`.`customer_id`'=>$customers])
                        ->andWhere('lv_salesorders.ordertype IN (1,2,3,4)')
                        ->andWhere('lv_items.deleted  = 1')
                        ->groupBy('model, ordernumber');	
            }
        
        $deletedIncomingPurchases = Purchase::find()->innerJoin('lv_items', '`lv_items`.`purchaseordernumber` = `lv_purchases`.`id`')
                    ->andWhere('lv_purchases.deleted = 1')->groupBy('lv_purchases.id');
        $dataProvider = new ActiveDataProvider([ 
    			'query' => $deletedItemsRequested,
    			'pagination' => ['pageSize' => 10],
    			]);
            $delItemsHtml = $this->renderPartial('_delete_items_req', [
                        'dataProvider' => $dataProvider,
                        'pagination' => ['pageSize' => 10],
            ]);
            
            $dataProvider = new ActiveDataProvider([ 
    			'query' => $deletedIncomingPurchases,
    			'pagination' => ['pageSize' => 10],
    			]);
            $delPurchasehtml = $this->renderPartial('_delete_purchasing', [
                        'dataProvider' => $dataProvider,
                        'pagination' => ['pageSize' => 10],
            ]);
            $_retArray = array('success' => true, 'items_deleted_html' => $delItemsHtml, 'purchase_deleted_html' => $delPurchasehtml, 'purchase_delete_count' => (int)$deletedIncomingPurchases->count(), 'items_delete_count' => (int)$deletedItemsRequested->count());
            Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
            //return view
            return $_retArray;
            exit();
        }

    /**
     * Finds the Purchase model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Purchase the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Purchase::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}