<?php

use yii\helpers\Html;
use yii\grid\GridView;
use app\models\Manufacturer;
use app\models\Models;
use app\models\Location;
use app\models\Itemlog;
use app\models\Item;

?>
<?=

GridView::widget([
    'dataProvider' => $dataProvider,
    'summary' => '',
    'formatter' => ['class' => 'yii\i18n\Formatter', 'nullDisplay' => 'No Results to display'],
    'columns' => [
        [
            'attribute' => 'model',
            'format' => 'raw',
            'headerOptions' => ['style' => 'text-align: left'],
            'contentOptions' => ['style' => 'text-align:left'],
            'value' => function($model) {
                $_model = Models::findOne($model['model']);
                $_manufacturer = Manufacturer::findOne($_model->manufacturer);
                return '<div style="line-height: 40px;"><b>' . $_manufacturer->name . ' ' . $_model->descrip . '</b></div>';
            }
        ],
        [
            'attribute' => 'serial',
            'label' => 'Serial/Tag Number',
            'headerOptions' => ['style' => 'text-align: left'],
            'contentOptions' => ['style' => 'text-align:left'],
            'format' => 'raw',
            'value' => function($model) {
            	$_serial_tag = (empty($model['serial'])) ? $model['tagnum'] : $model['serial'];
                $_url = Html::a($_serial_tag, ['/customers/serialdetails', 'id' => $model['id']], ['style' => 'color: #08c']);
                return '<div style="line-height: 40px;">' . $_url . '</div>';
            }
                ],
                [
                    'attribute' => 'location',
                    'label' => 'Transferred From',
                    'contentOptions' => ['style' => 'max-width:275px;'],
                    'format' => 'raw',
                    'value' => function($model) use ($customer) {
                $itemLog = Itemlog::find()->select('locationid')->where(['itemid' => $model->id, 'status' => array_search('Transferred', Item::$status)])->one();
                $location = Location::findOne($itemLog['locationid']);
                $output = '';
                if (!empty($location->storenum))
                    $output .= "Store#: " . $location->storenum . " ";
                if (!empty($location->storename))
                    $output .= $location->storename . ' ';
                //
                $output .= '<br/>'.$location->address . " " . $location->address2 . " " . $location->city . " " . $location->state . " " . $location->zipcode;
                return $output;
            }
                ],
                [
                    'attribute' => 'origin',
                    'label' => 'Transferred To',
                    'headerOptions' => ['style' => 'text-align: left'],
                    'contentOptions' => ['style' => 'max-width:275px;'],
                    'format' => 'raw',
                    'value' => function($model) use ($customer) {
//                $itemLog = Itemlog::find()->select('locationid')->where(['itemid' => $model->id])->orderBy(['status' => SORT_ASC])->one();
                $location = Location::findOne($model['location']);
                $output = '';
                if (!empty($location->storenum))
                    $output .= "Store#: " . $location->storenum . " ";
                if (!empty($location->storename))
                    $output .= $location->storename . ' ';
                //
                $output .= '<br/>'.$location->address . " " . $location->address2 . " " . $location->city . " " . $location->state . " " . $location->zipcode;
                return $output;
            }
                ],
                [
                    'label' => 'Date Transferred',
                    'attribute' => 'created_at',
                    'format' => 'raw',
                    'value' => function($model) {
                        $itemlog = Itemlog::find()->where(['status' => array_search('Transferred', Item::$status) , 'itemid' => $model->id])->one();
                        return (!empty($itemlog->created_at) && $itemlog->created_at != "0000-00-00 00:00:00") ? date('F d, Y g:i a', strtotime($itemlog->created_at)) : "-";
                    }
                ],
                [
                    'attribute' => 'created_by',
                    'label' => 'Transferred By',
                    'format' => 'raw',
                    'value' => function($model) {
                        $sql = 'SELECT lv_users.* FROM `lv_itemslog` inner join lv_users on lv_users.id = lv_itemslog.userid where itemid = :itemid and status = :status';
                        $connection = Yii::$app->getDb();
                        $command = $connection->createCommand($sql)
                                ->bindValue(':itemid', $model->id)
                                ->bindValue(':status', array_search('Transferred', Item::$status));
                        $data = $command->queryOne();
                        return $data['firstname'] . ' ' . $data['lastname'];
                    }
                ]
            ],
        ])
?>