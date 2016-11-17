<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use app\models\Location;
?>
<div class="user-view">
   
    <?=
    DetailView::widget([
        'model' => $model,
        'attributes' => [
            ['attribute' => 'customer_id',
                'label' => Yii::t('app', 'Customer'),
            ],
            ['attribute' => 'storenum',
                'label' => Yii::t('app', 'Store'),
                
            ],
            ['attribute' => 'address',
                'label' => Yii::t('app', 'Adress'),
            ],
            ['attribute' => 'address2',
                'label' => Yii::t('app', 'Adress2'),
            ],
            ['attribute' => 'country',
                'label' => Yii::t('app', 'Country'),
            ],
            ['attribute' => 'city',
                'label' => Yii::t('app', 'City'),
            ],
            ['attribute' => 'state',
                'label' => Yii::t('app', 'State'),
            ],
            ['attribute' => 'zipcode',
                'label' => Yii::t('app', 'Zip Code'),
            ],
            ['attribute' => 'phone',
                'label' => Yii::t('app', 'Phone'),
            ],
            ['attribute' => 'email',
                'label' => Yii::t('app', 'Email'),
            ]
        ],
    ])
    ?>


</div>
