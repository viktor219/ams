<div>
    <table width="100%">
        <tr>
            <td style="text-align: left;">
                <?php if (!empty($_media_customer->filename)) : ?>
                    <?php $target_file = Yii::getAlias('@webroot') . '/public/images/customers/' . $_media_customer->filename; ?>
                    <?php if (file_exists(\Yii::getAlias('@webroot') . '/public/images/customers/' . $_media_customer->filename)) : ?>		 
                        <?= Html::img($target_file, ['alt' => $customer->companyname, 'style' => 'cursor:pointer;max-width:250px;max-height:100px;']); ?>
                    <?php else : ?>
                        <span style="cursor:pointer;line-height: 40px;"><?php echo $customer->companyname; ?></span>
                    <?php endif; ?>		 
                <?php else : ?>
                <?php endif; ?>
                <!--<img src="http://assetenterprises.com/testing/live/public/images/layout/assetlogo-trans.png" />-->
            </td>
            <td style="vertical-align: bottom; text-align: right">
                <?= $header; ?>
            </td>
        </tr>
    </table>
    <div style="margin-top: 10px">
        <table width="100%" id="order-details" cellspacing="0" cellpadding="5">
            <tr>
                <th style="font-weight: bold">
                    Model
                </th>
                <th style="font-weight: bold">
                    Address
                </th>
                <th style="font-weight: bold">
                    Store Number
                </th>
                <th style="font-weight: bold">
                    Name
                </th>
                <th style="font-weight: bold">
                    Division
                </th>
                <th style="font-weight: bold">
                    Phone
                </th>
                <th style="font-weight: bold">
                    Qty At Division
                </th>
                <th style="font-weight: bold">
                    Qty On Location
                </th>
                <th style="font-weight: bold">
                    Confirmed Qty
                </th>
                <th style="font-weight: bold">
                    Total
                </th>
<!--                <th style="font-weight: bold">
                    Department
                </th>-->
            </tr>
            <?php if (count($pdfDatas) > 0): ?>
                <?php foreach ($pdfDatas as $model): ?>
                    <tr>
                        <td>
                            <?php echo $model['model']; ?>
                        </td>
                        <td>
                            <?php echo $model['address']; ?>
                        </td>
                        <td>
                            <?php echo $model['store']; ?>
                        </td>
                        <td>
                            <?php echo $model['name']; ?>
                        </td>
                        <td>
                            <?php echo $model['division']; ?>
                        </td>
                        <td>
                            <?php echo $model['phone']; ?>
                        </td>
                        <td>
                            <?php echo $model['stock_qty']; ?>
                        </td>
                        <td>
                            <?php echo $model['stock_progress']; ?>
                        </td>
                        <td>
                            <?php echo $model['stock_shipped']; ?>
                        </td>
                        <td>
                            <?php echo $model['total_qty']; ?>
                        </td>
<!--                        <td>
                            <?php //echo $model['department']; ?>
                        </td>-->
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="11" style="text-align: center"><i>No results found.</i></td>
                </tr>
            <?php endif; ?>
        </table>
    </div>
</div>
