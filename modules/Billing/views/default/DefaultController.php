<?php

namespace app\modules\Billing\controllers;

use Yii;
use yii\web\Controller;
use app\models\User;
use app\models\Item;
use app\modules\Orders\models\Order;
use app\models\Invoices;
use app\models\ShipmentsItems;
use yii\data\ActiveDataProvider;

class DefaultController extends Controller {

    public function actionIndex() {
        if (Yii::$app->user->identity->usertype != User::REPRESENTATIVE) {
            $query = Order::find()->select('lv_salesorders.*')
                    ->innerJoin('lv_items', '`lv_items`.`ordernumber` = `lv_salesorders`.`id`')
                    ->innerJoin('lv_locations', '`lv_locations`.`id` = `lv_salesorders`.`location_id`')
                    ->where(['status' => array_search('Shipped', Item::$status), 'lv_salesorders.deleted' => 0])
                    ->groupBy('lv_salesorders.id');
        } else {
            $customers = ArrayHelper::getColumn(UserHasCustomer::find()->where(['userid' => Yii::$app->user->id])->asArray()->all(), 'customerid');
            $query = Order::find()->select('lv_salesorders.*')
                    ->innerJoin('lv_items', '`lv_items`.`ordernumber` = `lv_salesorders`.`id`')
                    ->innerJoin('lv_locations', '`lv_locations`.`id` = `lv_salesorders`.`location_id`')
                    ->where(['status' => array_search('Shipped', Item::$status), '`lv_salesorders`.`customer_id`' => $customers, 'lv_salesorders.deleted' => 0])
                    ->groupBy('lv_salesorders.id');
        }
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => ['pageSize' => 15],
        ]);
        if (Yii::$app->request->isAjax) {	
            $html = $this->renderPartial('_billing', ['dataProvider' => $dataProvider]);
            $_retArray = array('html' => $html);
            Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
            return $_retArray;
        }
        return $this->render('index', ['dataProvider' => $dataProvider]);
    }
    
    public function actionInvoice($id){
        $invoice_query = Invoices::find()->where(['orderid' => $id]);
        $invoiceDataProvider = new ActiveDataProvider([
            'query' => $invoice_query,
            'pagination' => ['pageSize' => 15],
        ]);
        
        $notInvoiced_query = ShipmentsItems::find()
                ->innerJoin('lv_items', 'lv_items.id = lv_shipments_items.itemid')
                ->where(['status' => array_search('Shipped', Item::$status)])
                ->andWhere('lv_items.ordernumber = '. $id)
                ->groupBy('lv_shipments_items.shipmentid');
        $notInvoicedDataProvider = new ActiveDataProvider([
            'query' => $notInvoiced_query,
            'pagination' => ['pageSize' => 15],
        ]);
        $countNotInvoiced = $notInvoiced_query->count();
        return $this->render('invoice', ['invoiceDataProvider' => $invoiceDataProvider, 'notInvoicedDataProvider' => $notInvoicedDataProvider, 'countNotInvoiced' => $countNotInvoiced, 'order_id' => $id]);
    }
    
    public function actionCreateinvoice($id){
        $shipment_items = ShipmentsItems::find()
                ->innerJoin('lv_items', 'lv_items.id = lv_shipments_items.itemid')
                ->where(['status' => array_search('Shipped', Item::$status)])
                ->andWhere('lv_items.ordernumber = '. $id)
                ->groupBy('lv_shipments_items.shipmentid')->all();
        foreach($shipment_items as $shipment_item){
            $_item = Item::findOne($shipment_item->itemid);
            $_item->status = array_search('Ready to Invoice', Item::$status);
            $_order = Order::findOne($id);
            if($_item->save()){
                $invoiceCount = Invoices::find()->where(['orderid' => $id])->count();
                    $invoice_model = New Invoices;
                    $invoice_model->created_at = date('Y-m-d H:i:s');
                    $invoice_model->orderid = $id;
                    $invoice_model->invoicename = $_order->number_generated.'_'.($invoiceCount+1);
                    $invoice_model->generated = 1;
                    $invoice_model->save(false);
//                if(!$invoice_model->count()){ 
//                    $invoice_model = New \app\models\Invoices;
//                    $invoice_model->created_at = date('Y-m-d H:i:s');
//                    $invoice_model->orderid = $_order->id;
//                    $invoice_model->invoicename = $_order->number_generated.'_'.($invoice_model->count()+1);
//                }
                Yii::$app->getSession()->setFlash('success', 'Invoice has been created successfully.');
            }  else {
                Yii::$app->getSession()->setFlash('danger', 'There is some problem in creating invoice.');
            }
        }
        $this->redirect(['/billing/invoice?id='.$id]);
    }

}
