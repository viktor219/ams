<?php
    /**
     * Ajax Search Shipping model.
     * @return mixed
     */

namespace app\modules\Shipping\controllers\_actions;

use app\modules\Orders\models\Order;
use app\models\Customer;
use app\models\Location;
use app\models\CustomerSearch;
use app\models\Shipping;
use app\models\Shipment;
use app\models\ShipmentMethod;
use app\models\ShippingCompany;
use app\models\Item;
use app\models\Itemlog;
use app\models\Manufacturer;
use app\models\Medias;
use app\models\Models;
use app\models\ShipmentsItems;
use app\models\Itemsordered;
use app\models\Ordertype;
use app\models\ShippingSearch;
use app\models\User;
use app\models\ShipmentBoxDetail;
use Yii;
use yii\base\Action;
use yii\web\Controller;
use yii\data\Pagination;
use yii\web\NotFoundHttpException;
use yii\data\ActiveDataProvider;
use yii\helpers\Url;
use app\components\AccessRule;
use yii\filters\AccessControl;
use app\models\UserHasCustomer;
use yii\helpers\ArrayHelper;
use app\models\SystemSetting;
use app\models\CustomerSetting;
use app\models\SalesorderWs;
//use app\modules\Shipping\models\ShippingSearch;

class SearchInShipping extends Action
{
    public function run()
    {
        $_get = Yii::$app->request->get();

        $ordernumber = $_get['ordernumber'];
 
        if(Yii::$app->user->identity->usertype != 1) {
			$query = Item::find()->innerJoin('lv_salesorders', '`lv_salesorders`.`id` = `lv_items`.`ordernumber`')
								 ->where(['status'=>array_keys(Item::$shippingallstatus)])
                                 ->andFilterWhere(['like', 'number_generated', $ordernumber])
								 ->groupBy('ordernumber')
								 ->orderBy('id DESC');
		} else {
			$customers = ArrayHelper::getColumn(UserHasCustomer::find()->where(['userid'=>Yii::$app->user->id])->asArray()->all(), 'customerid');
			$query = Item::find()->innerJoin('lv_salesorders', '`lv_salesorders`.`id` = `lv_items`.`ordernumber`')
								 ->where(['status'=>array_keys(Item::$shippingallstatus)])
                                 ->andWhere(['`lv_salesorders`.`customer_id`'=>$customers])
                                 ->andFilterWhere(['like', 'number_generated', $ordernumber])
								 ->groupBy('ordernumber')
								 ->orderBy('id DESC');
		}
		
		$dataProvider = new ActiveDataProvider([
							'query' => $query,
							'pagination' => ['pageSize' => 15],
						]);

		$html = $this->controller->renderPartial('_inshipping', [
				                    'dataProvider' => $dataProvider,
				                ]);
        
        $_retArray = array('success' => true, 'html' => $html, 'count'=>$dataProvider->getTotalCount());
		Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

		return $_retArray;
    }
}