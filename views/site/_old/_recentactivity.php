<?php

use app\models\Models;
use app\models\Customer;
use app\models\Item;
use app\models\User;
use app\models\Recentactivity;
use app\modules\Orders\models\Order;
?>
<?php foreach ($recentActivities as $key => $recentActivity): ?>
    <?php $type = Recentactivity::$type[$recentActivity->type]; ?>
    <li>
        <div class="block">
            <div class="block_content">
                <h2 class="title">
                    <?= $type; ?>
                    <?php if ($type != "Items In Progress" && $type != "Items Received" && $type != "Items Shipped"): ?>
                        <?= ($recentActivity->is_new) ? "Created" : "Update"; ?>
                    <?php endif; ?>
                </h2>
                <div class="byline">
                    <?php
                    $user = User::findOne($recentActivity->user_id);
                    ?>
                    <span><?= $this->context->time_ago($recentActivity->created_at); ?></span> by <a><?= $user->firstname . ' ' . $user->lastname; ?></a>
                </div>
                <p class="excerpt">
                    <?php
                    $operation_type = ($recentActivity->is_new) ? "created." : "updated.";
                    if ($type == "Model") {
                        $model = Models::findOne($recentActivity->pk);
                        $url = Yii::$app->request->baseUrl."/inventory/update?id=".$model->id;
                        echo "<a href='".$url."'>" . $model->descrip . '</a> has been ' . $operation_type;
                    } else if ($type == "Order") {
                        $model = Order::findOne($recentActivity->pk);
                        $qty = Item::find()->where(['ordernumber' => $model->id])->count();
                        echo "<a href=''>" . $model->number_generated . "</a> has been " . $operation_type . " for " . $qty . " items.";
                    } else if ($type == "Priority Order") {
                        $model = Order::findOne($recentActivity->pk);
                        $qty = Item::find()->where(['ordernumber' => $model->id])->count();
                        echo "<a href='' class='red'>" . $model->number_generated . "</a> has been " . $operation_type . " for " . $qty . " items.";
                    } else if ($type == "Items Received" || $type == "Items In Progress" || $type == "Items Shipped") {
                        $item = Item::findOne($recentActivity->pk);
                        $model = Models::findOne($item->model);
                        $customer = Customer::findOne($recentActivity->customer_id);
                        $order = Order::findOne($item->ordernumber);
                        $man = \app\models\Manufacturer::findOne($model->manufacturer); 
                        $name = $model->descrip;
                        if(empty($name)){
                            $name = $man->name;
                        }
                        if(!empty($name)){
                            $name .="'s"; 
                        }
                        $url = Yii::$app->request->baseUrl."/inventory/update?id=".$model->id;
                        if ($type == "Items Received") {
                            $qty = Item::find()->where(['ordernumber' => $item->ordernumber, 'status' => array_search('Received', Item::$status)])->count();
                            echo $qty . " <a href='".$url."'>" . $name . "</a> have been received for " . $customer->companyname . " stock.";
                        } else if ($type == "Items In Progress") {
                            $qty = Item::find()->where(['ordernumber' => $item->ordernumber, 'status' => array_search('In Progress', Item::$status)])->count();
                            echo $qty . " <a href='".$url."'>" . $model->descrip . "</a> have been started for " . $customer->companyname . ".";
                        } else {
                            $qty = Item::find()->where(['ordernumber' => $item->ordernumber, 'status' => array_search('Shipped', Item::$status)])->count();
                            echo $qty . " <a href='".$url."'>" . $model->descrip . "</a> have been shipped for " . $customer->companyname . " <a href='javascript:void(0);'>" . $order->number_generated . "</a>.";
                        }
                    } else if ($type == "User") {
                        $user = User::findOne($recentActivity->pk);
                        $usertype = \app\models\Usertype::findOne($user->usertype);
                        if ($recentActivity->is_new || (!$recentActivity->is_new && $recentActivity->usertype > 0)) {
                            echo "<a href='javascript: void(0);'>" . $user->firstname . ' ' . $user->lastname . "</a> now has a " . $usertype->name . " account.";
                        } else {
                            echo "<a href='javascript: void(0);'>" . $user->firstname . ' ' . $user->lastname . "</a> has been updated.";
                        }
                    }
                    ?>
                </p>
            </div>
        </div>
    </li>
    <?php
    //if($i==3):
    //  break;
    //endif;
    //$i++;
    ?>
<?php endforeach; ?>
<!--							<li>
                                                                <div class="block">
                                                                        <div class="block_content">
                                                                                <h2 class="title">
                                                                <a>Model Creation</a>
                                                        </h2>
                                                                                <div class="byline">
                                                                                        <span>13 hours ago</span> by <a>Matt E.</a>
                                                                                </div>
                                                                                <p class="excerpt">Matt E. Created a new model.</a>
                                                                                </p>
                                                                        </div>
                                                                </div>
                                                        </li>-->
<!--							<li>
                                                                <div class="block">
                                                                        <div class="block_content">
                                                                                <h2 class="title">
                                                                <a>Equipment Repair</a>
                                                        </h2>
                                                                                <div class="byline">
                                                                                        <span>13 hours ago</span> by <a>Paul S.</a>
                                                                                </div>
                                                                                <p class="excerpt">Paul S. Started on IBM 4900-783 for Ahold</a>
                                                                                </p>
                                                                        </div>
                                                                </div>
                                                        </li>
                                                        <li>
                                                                <div class="block">
                                                                        <div class="block_content">
                                                                                <h2 class="title">
                                                                <a>Equipment Repair</a>
                                                        </h2>
                                                                                <div class="byline">
                                                                                        <span>13 hours ago</span> by <a>Will K.</a>
                                                                                </div>
                                                                                <p class="excerpt">Will K. Finished all 35 IBM 4610-2CRs for POS Surplus</a>
                                                                                </p>
                                                                        </div>
                                                                </div>
                                                        </li>-->