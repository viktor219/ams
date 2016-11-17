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
        <?php foreach ($pdfDatas as $index => $value): ?>
            <h4 style="text-align: center; margin: 10px">
                <?php echo $index; ?>
            </h4>
            <table width="100%" id="order-details" cellspacing="0" cellpadding="5">
                <tr>
                    <th style="font-weight: bold; font-size: 13px;">
                        Model
                    </th>
                    <th style="font-weight: bold; font-size: 13px;">
                        Address
                    </th>
                    <th style="font-weight: bold; font-size: 13px;">
                        Store Number
                    </th>
                    <th style="font-weight: bold; font-size: 13px;">
                        Name
                    </th>
                    <th style="font-weight: bold; font-size: 13px;">
                        Division
                    </th>
                    <th style="font-weight: bold; font-size: 13px;">
                        Phone
                    </th>
                    <th style="font-weight: bold; font-size: 13px;">
                        In Stock
                    </th>
                    <th style="font-weight: bold; font-size: 13px;">
                        In Progress
                    </th>
                    <th style="font-weight: bold; font-size: 13px;">
                        In Shipped
                    </th>
                    <th style="font-weight: bold; font-size: 13px;">
                        Total
                    </th>
                    <th style="font-weight: bold; font-size: 13px;">
                        Department
                    </th>
                </tr>
                <?php if (count($value) > 0): ?>
                    <?php foreach ($value as $model): ?>
                        <tr>
                            <td style="font-size: 12px;">
                                <?php echo $model['model']; ?>
                            </td>
                            <td style="font-size: 12px;">
                                <?php echo $model['address']; ?>
                            </td>
                            <td style="font-size: 12px;">
                                <?php echo $model['store']; ?>
                            </td>
                            <td style="font-size: 12px;">
                                <?php echo $model['name']; ?>
                            </td>
                            <td style="font-size: 12px;">
                                <?php echo $model['division']; ?>
                            </td>
                            <td style="font-size: 12px;">
                                <?php echo $model['phone']; ?>
                            </td>
                            <td style="font-size: 12px;">
                                <?php echo $model['stock_qty']; ?>
                            </td>
                            <td style="font-size: 12px;">
                                <?php echo $model['stock_progress']; ?>
                            </td>
                            <td style="font-size: 12px;">
                                <?php echo $model['stock_shipped']; ?>
                            </td>
                            <td style="font-size: 12px;">
                                <?php echo $model['total_qty']; ?>
                            </td>
                            <td style="font-size: 12px;">
                                <?php echo $model['department']; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="11" style="text-align: center"><i>No results found.</i></td>
                    </tr>
                <?php endif; ?>
            </table>
        <?php endforeach; ?>
    </div>
</div>
