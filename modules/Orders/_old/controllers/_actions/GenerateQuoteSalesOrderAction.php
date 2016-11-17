<?php
    /**
     * Print Quote Order PDF.
     * @return mixed
     */

namespace app\modules\Orders\controllers\_actions;

use yii\base\Action;
use app\models\QOrder;
use app\models\Customer;
use app\models\Location;
use app\models\QItemsordered;
use app\models\QShipment;
use app\models\ShippingCompany;
use app\models\ShipmentMethod;
use app\models\Medias;
use kartik\mpdf\Pdf;
use app\models\Ordertype;
use yii\web\NotFoundHttpException;
 
class GenerateQuoteSalesOrderAction extends Action
{
    public function run($id)
    {
    	$model = $this->findModel($id);
    	 
    	$shipment = QShipment::find()->where(['orderid'=>$model->id])->one();
    	
    	$shipping_method = ShipmentMethod::findOne($shipment->shipping_deliverymethod);
    	
    	$shipping_company = ShippingCompany::findOne($shipping_method->shipping_company_id);
    	
    	$customer = Customer::findOne($model->customer_id);
    	
    	$assetCustomer = Customer::findOne(4);
    	
    	$location = Location::findOne($model->location_id);
    	
    	$assetLocation = Location::findOne($assetCustomer->defaultshippinglocation);
    	
    	$_media_customer = Medias::findOne($customer->picture_id);
    	
    	$itemsordered = QItemsordered::find()->where(['ordernumber'=>$model->id])->all();
    	
    	$maxRows = 18;
    	
    	$taxRate = 10;
    	
    	$content = $this->controller->renderPartial('_qgenerate', [
    			'model'=>$model, 
    			'customer'=>$customer, 
    			'assetCustomer'=>$assetCustomer, 
    			'location'=>$location,
    			'assetLocation'=>$assetLocation,
    			'shipment'=>$shipment,
    			'shipping_method'=>$shipping_method,
    			'shipping_company'=>$shipping_company,
    			'_media_customer'=>$_media_customer, 
    			'itemsordered'=>$itemsordered, 
    			'maxRows'=>$maxRows,
    			'taxRate'=>$taxRate
    			]);
    	
    	$cssContent = '
    		.kv-heading-1, th, td {font-size:18px}
    		th
		 	{
		 		background: #08c;
    			color: #FFF;
    			padding: 5px;
    			padding-left: 10px;
    			padding-right: 10px;
    			text-align:center;
		 	}	
    		table {width:1350px;font-size:14px;border-collapse:collapse;margin-bottom:60px;}
    		.header_pdf th {background: none;color: #333;font-size:32px;}
    		.header_pdf tr{border:none;}
    		#sr_addresses tr{border:1px solid white;}
    		.header_pdf td{font-size:20px;}
    		.header_pdf_right{float: right;}
    		#shipping_methods tr {border:2px solid silver;}
    		#products tr {border:2px solid silver;}
    		.header_pdf tr{float:left;}
    		#shipping_methods td, #shipping_methods th, #products th {text-align:center;}
    		#products td {padding-right: 8px;}
    		table tr.no-border-row {border-bottom: none;} 
    		.pair-row {background: #BBB;}   		
    		.align_right {text-align:right;}
    		.align_left {text-align:left;}	
    		.border{border:1px solid silver;}
    		.no_border{border:none;}
    		#hd_txt_1 {font-family: "lohitkannada";}
    	';
    	
		$footer_content = $this->controller->renderPartial('_generate_footer');
    	
    	$pdf = \Yii::$app->pdf;
    	
    	$pdf->content = $content;
    	
    	$mpdf = $pdf->api; // fetches mpdf api
    	
    	$mpdf->WriteHTML($cssContent, 1, true, true);
    	
    	$mpdf->SetTitle('Generate SO#: ' . $model->number_generated);
    	
    	$mpdf->SetHeader(Ordertype::findOne($model->ordertype)->name . ' Order||SO#: ' . $model->number_generated);
    	
		$mpdf->SetFooter("|$footer_content|");
    	
	    // return the pdf output as per the destination setting
	    return $pdf->render(); 
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
    	if (($model = QOrder::findOne($id)) !== null) {
    		return $model;
    	} else {
    		throw new NotFoundHttpException('The requested page does not exist.');
    	}
    }
}