<?php 

	use common\helpers\CssHelper;
	use yii\helpers\Html;
	use yii\helpers\Url;
	use yii\grid\GridView;
	use app\models\User;
	use app\models\Customer;
	use app\models\UserHasCustomer;
	use app\models\Department;
?>

	<?=
    GridView::widget([
        'dataProvider' => $dataProvider,
        //'filterModel' => $searchModel,
        'summary' => false,
        'columns' => [
            //['class' => 'yii\grid\SerialColumn'],
            [
                'attribute' => 'firstname',
                'label' => Yii::t('app', 'Name'),
    			'format' => 'raw',
                'value' => function($model) {
                    return '<div style="line-height:40px;">' . $model['firstname'] . ' ' . $model['lastname'] . '</div>';
                }
            ],       
            ['attribute' => 'username',
                'label' => Yii::t('app', 'Username'),
                'format' => 'raw',
                'value' => function($model) {
                    return '<div style="line-height:40px;">' . $model['username'] . '</div>';
                }
            ],
            ['attribute' => 'email',
                'label' => Yii::t('app', 'Email'),
                'format' => 'raw',
                'value' => function($model) {
                	$output = (!empty($model['email'])) ? $model['email'] : '-';
                    return '<div style="line-height:40px;">' . $output . '</div>';
                }
            ],
            ['attribute' => 'usertype',
                'label' => Yii::t('app', 'Type'),
                'format' => 'raw',
                'value' => function($model) {
                    $_user_type = User::$status[$model->usertype];
                    if(Yii::$app->user->identity->usertype!==User::TYPE_CUSTOMER && $_user_type===1)
                    	$_user_type = 'Administrator';
                    return '<div style="line-height:40px;">' . $_user_type . '</div>';
                }
            ],   
            ['attribute' => 'usertype',
                'label' => Yii::t('app', 'Customer'),
                'format' => 'raw',
                'value' => function($model) {
                   $_customer_name = 'All';
                   $id = $model['id'];
                   $_my_customer = UserHasCustomer::find()->where(['userid' => $id])->all();
                   if(isset($_my_customer[0]['customerid'])){
                       $_check = $_my_customer[0]['customerid'];
                       $_my_customer_details = Customer::findOne($_check);
                       if(isset($_my_customer_details->companyname)){
                           $_customer_name = $_my_customer_details->companyname;
                       }
                   }
                   return '<div style="line-height:40px;">' . $_customer_name . '</div>';
                }
            ],
            ['attribute' => 'department',
                'label' => Yii::t('app', 'Department'),
                'format' => 'raw',
                'value' => function($model) {
                   $department = 'All';
                   if($model['department'] > 0){
                        $_my_department = Department::findOne($model['department']);
                        if(isset($_my_department->name)){
                            $department = $_my_department->name;
                        }
                   }
                   return '<div style="line-height:40px;">' . $department . '</div>';
                }
            ],
            ['class' => 'yii\grid\ActionColumn',
                'contentOptions' => ['style' => 'width:220px;'],
                'template' => '{view} {update} {delete} {viewlog}',
                'buttons' => [
                    'view' => function ($url, $model, $key) {
						$options = [
							'title' => Yii::t('app', 'View User'),
							'class' => 'btn btn-info viewUser',
							'type'=>'button',
							'uid'=>$model['id']
						];

						$url = 'javascript://';
						
						return Html::a('<span class="glyphicon glyphicon-eye-open" aria-hidden="true"></span>', $url, $options);						
                    },
                    'update' => function ($url, $model, $key) {
						$options = [
							'title' => Yii::t('app', 'Manage User'),
							'class' => 'btn btn-warning',
							'type'=>'button'
						];
						
						$url = \yii\helpers\Url::toRoute(['/users/update', 'id'=>$model['id']]);
						
						return Html::a('<span class="glyphicon glyphicon-edit" aria-hidden="true"></span>', $url, $options);
                    },
                    'delete' => function ($url, $model, $key) {
						$options = [
							'title' => Yii::t('app', 'Delete User'),
							'class' => 'btn btn-danger deleteUser',
							'type'=>'button',
							'uid'=>$model['id']
						];
						
						//$url = 'javascript://';
						$url = \yii\helpers\Url::toRoute(['/users/sdelete', 'id'=>$model['id']]);
						
						return Html::a('<span class="glyphicon glyphicon-trash" aria-hidden="true"></span>', $url, $options);                                               
                    },
                    'viewlog' => function ($url, $model, $key) {
						$options = [
							'title' => Yii::t('app', 'View [' . $model['firstname'] . ' ' . $model['lastname'] . '] Log'),
							'class' => 'btn btn-primary',
							'type'=>'button',
						];

						$url = \yii\helpers\Url::toRoute(['/userlog/index', 'id'=>$model['id']]);
												
						return Html::a('<span class="glyphicon glyphicon-globe" aria-hidden="true"></span>', $url, $options);
                    },
                        ]
                    ], // ActionColumn
                ], // columns
            ]);
            ?>