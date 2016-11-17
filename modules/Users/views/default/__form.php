<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\models\Customer;
use app\models\User;

?>
<style>
	.form-control{border-radius: 5px}
</style>
<?php $form = ActiveForm::begin(['options' => ['id'=>'add-user-form', 'enctype' => 'multipart/form-data']]); ?>
	<div class="col-lg-12 col-xs-12">
		<div class="x_panel" style="padding: 10px 10px;">
			<div class="x_title">
				<h2><i class="fa fa-bars"></i> <?= Html::encode($this->title) ?></h2>
				<ul class="nav navbar-right panel_toolbox">
					<li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a></li>
				</ul>
				<div class="clearfix"></div>
			</div>
			<div class="x_content" style="padding:0;margin-top:0;">
				<div class="" role="tabpanel" data-example-id="togglable-tabs">
					<div id="myTabContent" class="tab-content">	
						<?php if(!empty($profile_image)) :?>
							<div class="row">
								<div class="profile_pic">
									<img src="<?php echo $profile_image;?>" alt="..." style="height:225px;" class="img-circle profile_img">
								</div>
							</div>
						<?php endif;?>
					    <div class="row">
					        <div id="user_firstname_group" class="col-sm-6">
					            <label for="u_firstname" class="sr-only"></label>
					            <input value="<?php if(isset($user->firstname)) echo $user->firstname;?>" type="text" placeholder="First Name (Required)" name="u_firstname" id="u_firstname" class="form-control">
					        </div>
					        <div id="user_lastname_group" class="col-sm-6">
					            <label for="u_lastname" class="sr-only"></label>
					            <input value="<?php if(isset($user->lastname)) echo $user->lastname;?>" type="text" placeholder="Last Name (Required)" name="u_lastname" id="u_lastname" class="form-control">
					        </div>
					    </div>
					    <br>
					    <div class="row">
					        <div id="user_email_group" class="col-sm-6">
					            <label for="u_email" class="sr-only"></label>
					            <input value="<?php if(isset($user->email)) echo $user->email;?>" type="email" placeholder="Email (Required)" name="u_email" id="u_email" class="form-control">
					        </div>
					        <div id="user_username_group" class="col-sm-6">
					            <label for="u_username" class="sr-only"></label>
					            <input value="<?php if(isset($user->username)) echo $user->username;?>" type="text" placeholder="Username (Required)" name="u_username" id="u_username" class="form-control">
					        </div>
					    </div>
					    <br>
					    <div class="row">
					        <div id="user_password_group" class="col-sm-6">
					            <label for="u_hash_password" class="sr-only"></label>
					            <input value="" type="password" placeholder="<?php if($user->usertype == 9):?>********<?php else:?>Password (Required)<?php endif;?>" name="u_password" id="u_hash_password" class="form-control">
					        </div>
					        <div class="col-sm-6" id="user_type_group">	
					        	<?php if(Yii::$app->user->identity->usertype!=User::REPRESENTATIVE) :?>				            
					            	<label for="u_usertype" class="sr-only"></label>	         
	                                <select name="u_usertype" id ="u_usertype" class="form-control">
										<?php if(Yii::$app->user->identity->usertype!==User::TYPE_CUSTOMER) :?>
											<option selected="" disabled="" value="">Please Choose:</option>
											<option <?php if(isset($user->usertype) && $user->usertype == 1) echo "Selected";?> value="1">Customer</option>
											<option <?php if(isset($user->usertype) && $user->usertype == 2) echo "Selected";?> value="2">Receiving</option>
											<option <?php if(isset($user->usertype) && $user->usertype == 3) echo "Selected";?> value="3">Technician</option>
											<option <?php if(isset($user->usertype) && $user->usertype == 4) echo "Selected";?> value="4">Shipping</option>
											<option <?php if(isset($user->usertype) && $user->usertype == 8) echo "Selected";?> value="8">Purchasing</option>
											<option <?php if(isset($user->usertype) && $user->usertype == 5) echo "Selected";?> value="5">Billing</option>
											<option <?php if(isset($user->usertype) && $user->usertype == 6) echo "Selected";?> value="6">Sales</option>
											<option <?php if(isset($user->usertype) && $user->usertype == 7) echo "Selected";?> value="7">Admin</option>
											<option <?php if(isset($user->usertype) && $user->usertype == 9) echo "Selected";?> value="9">Representative</option>
										<?php else :?>
											<option <?php if(isset($user->usertype) && $user->usertype == 1) echo "Selected";?> value="1">Administrator</option>
											<option <?php if(isset($user->usertype) && $user->usertype == 9) echo "Selected";?> value="9">Representative</option>
										<?php endif;?>
						            </select>
					            <?php endif;?>
					        </div>
					    </div>
                                           <?php if($user->usertype != 9 && Yii::$app->user->identity->usertype != 1):?> 
                                            <div class="row" style="margin-top: 10px">
						<div class="col-sm-6" id="user-customer-group" <?php if($user->isNewRecord || (!$user->isNewRecord && $user->usertype != 1)): ?>style="display:none;"<?php endif;?>>
							<select name="customers[]" id="selectCustomers" multiple="multiple">
								<?php foreach(Customer::find()->all() as $customer) :?>
									<option value='<?php echo $customer->id;?>' <?php if(!$user->isNewRecord && in_array($customer->id, $customers)):?>selected<?php endif;?>><?php echo $customer->companyname;?></option>
								<?php endforeach;?>
							</select>
						</div>
                                                <div id="upload-group" class="col-sm-6">
                                                        <span class="btn btn-default btn-file">
                                                                Upload Logo <input type="file" name="u_logo" id="fileToUpload">
                                                        </span>
						</div>
                                            </div>
                                            <?php endif; ?>
					    <br>
					    <div class="row" id="veryDepartment" <?php if(!isset($user->usertype)){?>style="display: none;<?php } ?>   <?php if(isset($user->usertype) && $user->usertype != 3){?>style="display: none;<?php } ?>">
					        <div id="location_email-group" class="col-sm-6">
					           
					        </div>
					        <div class="col-sm-6">
					            
					            <label for="u_department" class="sr-only"></label>
					            <select class="form-control" id="u_department" name="u_department">
					                <option value="" disabled="" selected="">Select Department:</option>
					                <?php
					                if (isset($departments) && count($departments) > 0 && !empty($departments)) {
					
					                    for ($k = 0; $k < count($departments); $k++) {
					                        ?>
					
					                        <option <?php if(isset($user->department) && $user->department == $departments[$k]['id']) echo "Selected";?> value="<?php echo $departments[$k]['id']; ?>"><?php echo $departments[$k]['name']; ?></option>
					
					                        <?php
					                    }
					                }
					                ?>
					            </select>
					            
					        </div>
					    </div>
						<div class="row-margin"></div>
						<div class="row row-margin">
							<div class="col-md-12 text-right">
								<button onClick="redirectUser();" type="button" class="btn btn-primary"><span class="glyphicon glyphicon-remove"></span> Cancel</button>
								<?php if($user->isNewRecord) :?>
									<button class="btn btn-success" type="submit"><span class="glyphicon glyphicon-save"></span> Create</button>
								<?php else :?>
									<button class="btn btn-warning" type="submit"><span class="glyphicon glyphicon-edit"></span> Update</button>
								<?php endif;?>
							</div>
						</div>						    
				</div>
			</div>
		</div>
	</div>
</div>
<?php ActiveForm::end(); ?>
<script src="<?php echo Yii::$app->request->baseUrl;?>/public/js/uc/user_create.js"></script>
<link href="<?php echo Yii::$app->request->baseUrl;?>/public/css/stack_index.css" rel="stylesheet">