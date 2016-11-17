<?php

namespace app\controllers;

use Yii;
use app\models\Itemspurchased;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use app\models\Item;

/**
 * ItemspurchasedController implements the CRUD actions for Itemspurchased model.
 */
class ItemspurchasedController extends Controller
{

    /**
     * Lists all Itemspurchased models.
     * @return mixed
     */
    public function actionIndex()
    {
        $dataProvider = new ActiveDataProvider([
            'query' => Itemspurchased::find(),
        ]);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Itemspurchased model.
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
     * Creates a new Itemspurchased model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Itemspurchased();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing Itemspurchased model.
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
     * Deletes an existing Itemspurchased model.
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
        $model = Item::findOne($id);
		
		$items = Item::find()->where(['status'=>array_search('Requested', Item::$status), 'ordernumber'=>$model->ordernumber, 'model'=>$model->model])->all();
				
		foreach($items as $item)
		{
			$item->deleted = 1;
			
			if($item->save()){
				$_message = 'Items Requested has been deleted successfully!';
				Yii::$app->getSession()->setFlash('danger', $_message);      
			} else {
				$_message = 'There is a problem in deleting User!';
				Yii::$app->getSession()->setFlash('warning', $_message);     
			}
		}
        return $this->redirect(['/purchasing/index']);
    }
    /**
     * Finds the Itemspurchased model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Itemspurchased the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Itemspurchased::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}