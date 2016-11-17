<?php
/**
 * Displays a single Order model.
 * @param integer $id
 * @return mixed
 */

namespace app\modules\Orders\controllers\_actions;

use yii\base\Action;
use app\modules\Orders\models\Order;
use yii\web\NotFoundHttpException;

class ViewAction extends Action
{
    public function run($id)
    {
        return $this->controller->render('view', [
            'model' => $this->findModel($id),
        ]);
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