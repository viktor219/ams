<?php
    /**
     * Updates an existing Order model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */

namespace app\modules\Orders\controllers\_actions;

use Yii;
use yii\base\Action;
use app\modules\Orders\models\Order;
use app\models\SystemSetting;
use yii\web\NotFoundHttpException;

class UpdateAction extends Action
{
    public function run($id)
    {
    	$model = $this->findModel($id);
    	
    	if ($model->load(Yii::$app->request->post()) && $model->save()) {
    		return $this->controller->redirect(['view', 'id' => $model->id]);
    	} else {
    		return $this->controller->render('update', [ 
    				'model' => $model,
    				'assetSetting' => SystemSetting::find()->one()
    				]);
    	}
    }
    
    /**
     * Finds the Order model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Order the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
    	if (($model = Order::findOne($id)) !== null) {
    		return $model;
    	} else {
    		throw new NotFoundHttpException('The requested page does not exist.');
    	}
    }
}