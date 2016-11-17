<?php

use app\models\Item;
use app\models\Manufacturer;
use app\models\Models;
use app\models\Itemlog;
use app\models\User;
?>
<?php if (count($_awaiting_delivery_items)): ?>
    <ul class="to_do">
        <?php foreach ($_awaiting_delivery_items as $_awaiting_delivery_item) : ?>
            <?php
            $item = Item::findOne($_awaiting_delivery_item->itemid);
            $model = Models::findOne($item->model);
            $manufacturer = Manufacturer::findOne($model->manufacturer);
            $itemlog = Itemlog::find()->where(['itemid' => $item->id])->one();
            $user = User::findOne($itemlog->userid);
            ?>
            <li>
                <p><input type="checkbox" class="flat awaiting_delivery_item" id="awaiting-delivery-item_<?php echo $_awaiting_delivery_item->itemid; ?>"/> <?php echo (isset($model->aei)) ? $model->aei . ' -' : ''; ?> <?php echo $manufacturer->name . ' ' . $model->descrip; ?> for <?php echo $user->firstname; ?> <?php echo strtoupper($user->lastname[0]); ?></p>
            </li>							
        <?php endforeach; ?>
        <?php foreach ($_delivered_items as $_delivered_item) : ?>
            <?php
            //echo "here";
            $model = Models::findOne($_delivered_item->model);
            $manufacturer = Manufacturer::findOne($model->manufacturer);
            $itemlog = Itemlog::find()->where(['itemid' => $_delivered_item->id])->one();
            $user = User::findOne($itemlog->userid);
            ?>
            <li>
                <p><input type="checkbox" class="flat awaiting_delivered_item" checked="checked" id="awaiting-delivery-item_<?php echo $_delivered_item->id; ?>"/> <?php echo (isset($model->aei)) ? $model->aei . ' -' : ''; ?> <?php echo $manufacturer->name . ' ' . $model->descrip; ?> for <?php echo $user->firstname; ?> <?php echo strtoupper($user->lastname[0]); ?></p>
            </li>							
        <?php endforeach; ?>
    </ul>
<?php else: ?>
    <div class="items-delivered text-center">
        All items have been delivered!
    </div>
    <div class="text-center">
        <span class="glyphicon glyphicon-ok" style="color: #1abb9c; font-size: 25px;"></span>
    </div>
<?php endif; ?>