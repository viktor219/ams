<?php
    /**
     * Lists all Order models.
     * @return mixed
     */

namespace app\modules\Orders\controllers\_actions;

use Yii;
use yii\base\Action;
use app\models\QOrder;
use app\models\Ordertype;
use yii\data\ActiveDataProvider;
use yii\web\NotFoundHttpException;

class QLoadAction extends Action
{
    public function run()
    {
    	$_post = Yii::$app->request->get();
    	
    	if (Yii::$app->request->isAjax) {
    		$_retArray = array('success' => FALSE, 'html' => '');
    		if (!isset($_post['type'])) {
    			$_retArray = array('success' => FALSE, 'html' => 'Something is wrong! Plese try again!');
    			echo json_encode($_retArray);
    			exit();
    		}
    		$type = ucfirst($_post['type']);
    			
    		if($type=="All"){
    			$query = QOrder::find()->select('lv_qsalesorders.*')
    			->innerJoin('lv_qitemsordered', '`lv_qitemsordered`.`ordernumber` = `lv_qsalesorders`.`id`')
				->where(['deleted' => 0])
    			->groupBy('`lv_qitemsordered`.`ordernumber`');
    		}
    		else
    		{
    			$query = QOrder::find()->select('lv_qsalesorders.*')
    			->innerJoin('lv_qitemsordered', '`lv_qitemsordered`.`ordernumber` = `lv_qsalesorders`.`id`')
    			->where([
    					'lv_qsalesorders.ordertype' => Ordertype::findOne(['name'=>$type])->id, 'deleted' => 0
    					])
    					->groupBy('`lv_qitemsordered`.`ordernumber`');
    		}
    		$dataProvider = new ActiveDataProvider([
    				'query' => $query,
    				'pagination' => ['pageSize' => 15],
    				'sort'=> ['defaultOrder' => ['shipby'=>SORT_ASC]]
    				]);
    			
    		$html = $this->controller->renderPartial('_qorder', [
						'dataProvider' => $dataProvider,
    				]);
    			
    		$_retArray = array('success' => true, 'html' => $html);
    			
    		Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
    		//return view
    		return $_retArray;
    		exit();
    	} else {
    		 
    		throw new NotFoundHttpException('The requested page does not exist.');
    	}
    }
}