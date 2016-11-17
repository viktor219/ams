<?php

use yii\widgets\DetailView;
?>
<div class="row row-margin">
    <div class="form-group">
        <div class="col-md-12">
            <?=
            DetailView::widget([
                'model' => $shipmentboxdetail,
                'attributes' => [
                    [
                        'attribute' => 'length',
                    ],
                    [
                        'attribute' => 'depth',
                    ],
                    [
                        'attribute' => 'height',
                    ],
                    [
                        'attribute' => 'weight',
                    ],
                ],
            ])
            ?>
        </div>
    </div>
</div>