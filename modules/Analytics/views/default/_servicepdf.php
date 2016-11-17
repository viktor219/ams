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
                <th style=" font-weight: bold">
                    Model
                </th>
                <th style="font-weight: bold">
                    Serial Number
                </th>
                <th style="font-weight: bold">
                    Status
                </th>
                <th style="font-weight: bold">
                    Incoming Tracking
                </th>
                <th style="font-weight: bold">
                    Outgoing Tracking
                </th>                
                <th style="font-weight: bold">
                    Tagnumber
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
                    Created At
                </th>
                <th style="font-weight: bold">
                    Created By
                </th>
                <th>
                    Days Open
                </th>
            </tr>
            <?php if (count($pdfDatas) > 0): ?>
                <?php foreach ($pdfDatas as $model): ?>
                    <tr>
                        <td>
                            <?php echo $model['model']; ?>
                        </td>
                        <td>
                            <?php echo $model['serial']; ?>
                        </td>
                        <td>
                            <?php echo $model['status']; ?>
                        </td>
                        <td>
                            <?php echo $model['returntracking']; ?>
                        </td>
                        <td>
                            <?php echo $model['outgoingtracking']; ?>
                        </td>
                        <td>
                            <?php echo $model['tagnum']; ?>
                        </td>
                        <td>
                            <?php echo $model['origin']; ?>
                        </td>
                        <td>
                            <?php echo $model['store_number']; ?>
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
                            <?php echo $model['created_at']; ?>
                        </td>
                        <td>
                            <?php echo $model['created_by']; ?>
                        </td>
                        <td>
                            <?= $model['open_days']; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="12" style="text-align: center"><i>No results found.</i></td>
                </tr>
            <?php endif; ?>
        </table>
    </div>
</div>
