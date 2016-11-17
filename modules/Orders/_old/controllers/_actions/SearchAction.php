<?php
    /**
     * Ajax Search Order model.
     * @return mixed
     */

namespace app\modules\Orders\controllers\_actions;

use Yii;
use yii\base\Action;
use app\modules\Orders\models\OrderSearch;
use yii\web\NotFoundHttpException;

class SearchAction extends Action
{
    public function run()
    {
    	$_post = Yii::$app->request->get();
    	 
    	//if (Yii::$app->request->isAjax) {
    		$_retArray = array('success' => FALSE, 'html' => '');
    		if (!isset($_post['query'])) {
    			$_retArray = array('success' => FALSE, 'html' => 'Something is wrong! Plese try again!');
    			echo json_encode($_retArray);
    			exit();
    		}
    	
    		$type = $_post['query'];
    	
    		$searchModel = new OrderSearch();
    		$dataProvider = $searchModel->search(['OrderSearch'=>['number_generated'=>trim($type)]]);
    		 
    		$html = $this->controller->renderPartial('_order', [
    				'dataProvider' => $dataProvider,
    				]);
    	
    		$_retArray = array('success' => true, 'html' => $html, 'count'=>$dataProvider->getTotalCount());
    		//echo json_encode($_retArray);
    		//exit();
    		Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
    		//return view
    		return $_retArray;
    		exit();
    	/*} else {
    		 
    		throw new NotFoundHttpException('The requested page does not exist.');
    	}*/
    }
}