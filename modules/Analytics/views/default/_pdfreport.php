
<div style="width: 80%; margin: 0 auto;">
    <table width="100%">
        <tr>
            <td>
                <img src="http://localhost/assetms/public/images/layout/assetlogo-trans.png" />
            </td>
            <td style="vertical-align: bottom; text-align: right">
            </td>
        </tr>
    </table>
    <div style="margin-top: 10px">
        <table width="100%" id="order-details" cellspacing="0">
            <tr>
                <th style="color: #a94442; font-weight: bold">
                    Model
                </th>
                <th style="color: #a94442; font-weight: bold">
                    Serial Number
                </th>
                <th style="color: #a94442; font-weight: bold">
                    Address
                </th>
                <th style="color: #a94442; font-weight: bold">
                    Store Number
                </th>
                <th style="color: #a94442; font-weight: bold">
                    Name
                </th>
                <th style="color: #a94442; font-weight: bold">
                    Division
                </th>
                <th style="color: #a94442; font-weight: bold">
                    Phone
                </th>
                <th style="color: #a94442; font-weight: bold">
                    Created At
                </th>
                <th style="color: #a94442; font-weight: bold">
                    Created By
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
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="9" style="text-align: center"><i>No results found.</i></td>
                </tr>
            <?php endif; ?>
        </table>
    </div>
    <div class="footer" style="font-size: 12px; text-align: center;margin-top:15px;">
        <div class="line_one" style="border-bottom: 1px solid silver;">Should you have any questions, please contact us at 864.331.8678</div>
        <div class="line_two">3431 N. Industrial Dr., Simpsonville, SC 29681</div>
        <div class="line_three">Tel: 864.331.8678  E-mail: info@assetenterprises.com Web: www.AssetEnterprises.com</div>
    </div>
</div>
