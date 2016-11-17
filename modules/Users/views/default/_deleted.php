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
				$_user_type = "Customer";
				if (isset($model['usertype']) && $model['usertype'] == 1) {
					$_user_type = "Customer";
				} else if (isset($model['usertype']) && $model['usertype'] == 2) {
					$_user_type = "Receiving";
				} else if (isset($model['usertype']) && $model['usertype'] == 3) {
					$_user_type = "Technician";
				} else if (isset($model['usertype']) && $model['usertype'] == 4) {
					$_user_type = "Shipping";
				} else if (isset($model['usertype']) && $model['usertype'] == 5) {
					$_user_type = "Billing";
				} else if (isset($model['usertype']) && $model['usertype'] == 6) {
					$_user_type = "Sales";
				} else if (isset($model['usertype']) && $model['usertype'] == 7) {
					$_user_type = "Admin";
				} else if (isset($model['usertype']) && $model['usertype'] == 8) {
					$_user_type = "Purchasing";
				}
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
			'contentOptions' => ['style' => 'width:220px;', 'class' => 'action-buttons'],
			'template' => '{revert} {delete}',
			'buttons' => [
				'revert' => function ($url, $model, $key) {
						$options = [
								'title' => 'Revert',
								'class' => 'btn btn-info revertUser',
						];
						$url = \yii\helpers\Url::toRoute(['/users/revert', 'id'=>$model->id]);

						return Html::a('<span class="glyphicon glyphicon-repeat" aria-hidden="true"></span>', $url, $options);
				},
				'delete' => function ($url, $model, $key) {
						$options = [
								'title' => 'Delete',
								'class' => 'btn btn-danger deleteUser',
								//'data-method' => 'post'
						];
						$url = \yii\helpers\Url::toRoute(['/users/delete', 'id'=>$model->id]);

						return Html::a('<span class="glyphicon glyphicon-remove" aria-hidden="true"></span>', $url, $options);
				}
					]
				], // ActionColumn
			], // columns
		]);
		?>