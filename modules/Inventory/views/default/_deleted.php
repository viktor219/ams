<?php

use yii\helpers\Html;
use yii\grid\GridView;
use app\models\ModelAssembly;
use app\models\Manufacturer;
use app\models\Department;
use app\models\Category;
use app\models\Inventory;
use app\models\Partnumber;
use app\models\Item;
use app\models\Models;
use app\models\Medias;
use app\models\Customer;
use app\models\User;
use yii\widgets\Pjax;
use yii\helpers\ArrayHelper;
use yii\models\UserHasCustomer;
?>

<?=

GridView::widget([
    'dataProvider' => $dataProvider,
    'formatter' => ['class' => 'yii\i18n\Formatter', 'nullDisplay' => ''],
    'summary' => '',
    'columns' => [
        [
            'attribute' => 'imagepath',
            'label' => 'Thumbnail',
            'format' => 'raw',
            'value' => function($model) {
                if ($model['filename'])
                    return Html::img(Yii::getAlias('@web') . '/public/images/models/' . $model['filename'], ['alt' => 'logo', 'onClick' => 'ModelsViewer(' . $model['id'] . ');', 'height' => '33px']);
            }
                ],
                [
                    'attribute' => 'aei',
                    'label' => 'Part Numbers',
                    'format' => 'raw',
                    'value' => function($model) {
                        if (!empty($model['aei']))
                            return '<a tabindex="0" class="btn btn-default popup-marker" data-content = "" id="partitem-popover_' . $model['id'] . '" data-poload="' . Yii::$app->request->baseUrl . '/ajaxrequest/getinventorypartnumbers?modelid=' . $model['id'] . '" role="button" data-html="true" data-placement="right" data-toggle="popover" data-animation="true" data-trigger="focus" data-original-title="Owners & Parts"> ' . $model['aei'] . ' </a>';
                        else
                            return "<div style='line-height: 40px;'>No Part Number</div>";
                    },
                    'filter' => false,
                ],
                [
                    'attribute' => 'modelname',
                    'label' => 'Model',
                    'format' => 'raw',
                    'value' => function($model) {
                        return "<div style='line-height: 40px;'>" . $model['name'] . " " . $model['descrip'] . "</div>";
                    },
                ],
                [
                    'label' => 'Inventory',
                    'format' => 'raw',
                    'value' => function($model) {
                        $content = "";
                        $sum = 0;
                        if ($model['assembly'] == 1) {
                            if (Yii::$app->user->identity->usertype == User::REPRESENTATIVE) {
                                $customers = ArrayHelper::getColumn(UserHasCustomer::find()->where(['userid' => Yii::$app->user->id])->asArray()->all(), 'customerid');
                                if (!count($customers)) {
                                    $customers = array(-1);
                                }
                                $number_items = ModelAssembly::find()
                                        ->innerJoin('lv_partnumbers', '`lv_partnumbers`.`id` = `lv_model_assemblies`.`partid`')
                                        ->where(['modelid' => $model->id, 'customer' => $customers])
                                        ->sum('quantity');
                            } else {
                                $number_items = ModelAssembly::find()->where(['modelid' => $model->id])->sum('quantity');
                            }
//									$items = ModelAssembly::find()->where(['modelid'=>$model->id])->all();
                            $nbr_items_in_stock = Item::find()
                                    ->innerJoin('lv_model_assemblies', '`lv_model_assemblies`.`partid` = `lv_items`.`model`')
                                    ->where(['modelid' => $model->id])
                                    ->andwhere(['status' => array_search('In Stock', Item::$status)]);
//                                                                ->andwhere('status IN ('.array_search('In Stock', Item::$status).', '.array_search('Ready to ship', Item::$status).')')
                            if (Yii::$app->user->identity->usertype == User::REPRESENTATIVE) {
                                $customers = ArrayHelper::getColumn(UserHasCustomer::find()->where(['userid' => Yii::$app->user->id])->asArray()->all(), 'customerid');
                                if (!count($customers)) {
                                    $customers = array(-1);
                                }
                                $nbr_items_in_stock->andWhere(['customer' => $customers]);
                            }
                            $nbr_items_in_stock->count();
                            $sum = ($number_items != 0) ? $nbr_items_in_stock / $number_items : 0;
//									//$sm =0;
//									foreach($items as $item)
//									{
//										$customers = Item::find()->select('customer')->where(['model'=>$item->partid])->andwhere('status IN ('.array_search('In Stock', Item::$status).', '.array_search('Ready to ship', Item::$status).')')->groupBy('customer')->all();
//										if($nbr_items_in_stock>0) {
//											foreach($customers as $customer) {
//												foreach(Item::$inventorystatus as $key=>$value)
//												{
//													$qty = Item::find()->where(['model'=>$item->partid, 'customer'=>$customer->customer, 'status' => $key])->count();
//													$qty = ($nbr_items_in_stock!=0) ? ($qty * $sum / $nbr_items_in_stock) : 0;
//													//$sm += $qty;
//													if( $qty > 0) {
//														$customername = Customer::findOne($customer->customer)->companyname;
//														$_model = Models::findOne($item->partid);
//														$_manufacturer = Manufacturer::findOne($_model->manufacturer);
//														$newline = '(' . number_format($qty, 2) . ') ' . $_manufacturer->name . ' ' . $_model->descrip . ' '. $value . ' ('.$customername.')';
//														if($name!=="" && strpos($content, $newline) === false)
//															$content .= $newline . "<br/>";
//													}
//												}
//											}
//										}
//									}	
                            //echo $sm;							
                        } else {
                            /* $sum_in_stock = Item::find()->where(['model'=>$model->id, 'status'=>array_search('In Stock', Item::$status)])->count();
                              $sum_picked = Item::find()->where(['model'=>$model->id, 'status'=>array_search('In Progress', Item::$status)])->count();
                              $sum_in_progress = Item::find()->where(['model'=>$model->id, 'status'=>array_search('Ready to ship', Item::$status)])->count();
                              $sum = $sum_in_stock + $sum_in_progress;
                              $customers = Item::find()->select('customer')->where(['model'=>$model->id])->andwhere('status IN ('.array_search('In Stock', Item::$status).', '.array_search('Ready to ship', Item::$status).')')->groupBy('customer')->all();
                              foreach($customers as $customer)
                              {
                              foreach(Item::$inventorystatus as $key=>$value)
                              {
                              $qty = Item::find()->where(['model'=>$model->id, 'customer'=>$customer->customer, 'status' => $key])->count();
                              $customername = Customer::findOne($customer->customer)->companyname;
                              if( $qty > 0)
                              $content .= "Qty: $qty $value ($customername) <br/>";
                              }
                              } */
                            $sum_in_stock = $model['instock_qty'];
//                                                $sum_in_progress = $model['inprogress_qty'];
//                                                $sum_readytoship = $model['readytoship_qty'];
                            $sum = $sum_in_stock;
//                                                $sum = $sum_in_stock + $sum_readytoship + $sum_in_progress;	 
//									$query = "
//											SELECT DISTINCT(customer), companyname, status, COUNT(status) as nbr_per_status FROM lv_items
//												INNER JOIN lv_customers ON lv_items.customer = lv_customers.id
//											    WHERE model=$model[id] 
//														AND status IN (".array_search('In Stock', Item::$status).", ".array_search('Ready to ship', Item::$status).", ".array_search('In Progress', Item::$status).")
//											    GROUP BY status, customer
//												ORDER BY companyname
//											";
//									$connection = Yii::$app->getDb();
//									
//									$command = $connection->createCommand($query, [':model'=> $model['id']]);
//									
//									$rows = $command->queryAll();
//									
//									foreach ($rows as $row)
//									{
//										$content .= "Qty: $row[nbr_per_status] " . Item::$inventorystatus[$row['status']] . " ($row[companyname]) <br/>";
//									}										
                        }
//								if(empty($content))
//									$content = "No Informations found";
                        return '<a tabindex="0" class="btn btn-default popup-marker" id="item-popover_' . $model['id'] . '" role="button" data-html="true" data-placement="right" data-toggle="popover" data-content = "" data-animation="true" data-trigger="focus" data-poload="' . Yii::$app->request->baseUrl . '/ajaxrequest/getinventorydata?id=' . $model['id'] . '" data-original-title="' . $model['name'] . ' ' . $model['descrip'] . '"> ' . $sum . ' </a>';
                    },
                        ],
                        [
                            'attribute' => 'assembly',
                            'label' => 'Assembly',
                            'format' => 'raw',
                            'value' => function($model) {
                                $output = ($model['assembly']) ? 'Yes' : 'No';
                                return "<div style='line-height: 40px;'>" . $output . "</div>";
                            },
                        ],
                        [
                            'class' => 'yii\grid\ActionColumn',
                            'template' => '{revert}',
                            'contentOptions' => ['style' => 'width:220px;'],
                            'buttons' => [
                                'revert' => function ($url, $model, $key) {
                                    $options = [
                                        'title' => 'Revert',
                                        'class' => 'btn btn-info revertInventory',
                                    ];
                                    $url = \yii\helpers\Url::toRoute(['/inventory/revert', 'id' => $model['id']]);

                                    return Html::a('<span class="glyphicon glyphicon-repeat" aria-hidden="true"></span>', $url, $options);
                                },
                                    ],
                                ],
                            ],
                        ]);
                        