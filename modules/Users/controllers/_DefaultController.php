<?php

namespace app\modules\Users\controllers;

use Yii;
use yii\data\ActiveDataProvider;
use app\components\AccessRule;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\helpers\ArrayHelper;
use yii\data\Pagination;
use app\models\User;
use app\models\Customer;
use app\models\UserHasCustomer;
use app\models\UserCreate;
use app\models\UserSearch;
use app\models\Department;
use app\models\Usertype;
use app\models\Users;
use app\models\Medias;
use yii\web\NotFoundHttpException;

class DefaultController extends Controller {
        const FILE_UPLOAD_PATH_USER = "/public/images/users/";
        const FILE_UPLOAD_PATH = "/public/images/";
    
	public function behaviors()
	{
		return [
			'access' => [
				'class' => AccessControl::className(),
				// We will override the default rule config with the new AccessRule class
				'ruleConfig' => [
					'class' => AccessRule::className(),
				],
				'only' => [
							'index','create', 'update', 'view', 'delete',
							'load', 'tload', 'search', 'createdepartment',
							'editdepartment', 'showdepartments', 'profile', 'getdeleted', 'revert', 'sdelete'
						],
				'rules' => [
					[
						'actions' => [
							'index','create', 'update', 'view', 'delete',
							'load', 'tload', 'search', 'createdepartment',
							'editdepartment', 'showdepartments', 'profile', 'getdeleted', 'revert', 'sdelete'						
						],
						'allow' => true,
						// Allow few users
						'roles' => [
							User::TYPE_ADMIN,
							User::TYPE_CUSTOMER_ADMIN,
							User::TYPE_CUSTOMER
						],
					],
					[
						'actions' => [
							'index', 'update'
						],
						'allow' => true,
						// Allow few users
						'roles' => [
							User::REPRESENTATIVE
						],						
					]
				],
			]
		];
	}

    public function actionIndex() {
    	if(Yii::$app->user->identity->usertype == User::REPRESENTATIVE) {
    		return $this->redirect(['/users/update', 'id'=>Yii::$app->user->id]);
    	} else {
        /**
         * Mobile or Desktop Detetcion
         */
        /*if (Yii::$app->mobileDetect->isMobile()) {
        	$searchModel = new UserSearch();
        	$dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        	
        	return $this->render('index_mobile', [
        			'searchModel' => $searchModel,
        			'dataProvider' => $dataProvider,
        			]);
        } else {*/
			$departments = Department::find()->select('lv_departements.*')->join('INNER JOIN', 'lv_users', 'lv_users.department =lv_departements.id')->groupBy('department')->all();
			
			$usertypes = Usertype::find()->select('lv_usertype.*')->join('INNER JOIN', 'lv_users', 'lv_users.usertype =lv_usertype.id')->groupBy('usertype')->all();

			if(Yii::$app->user->identity->usertype != 1)
			{			
				$_find_projects = Customer::find()->select('lv_customers.*')->join('INNER JOIN', 'lv_user_has_customer', 'lv_user_has_customer.customerid =lv_customers.id')
	                                ->innerJoin('lv_users', 'lv_users.id =lv_user_has_customer.userid')
	                                ->groupBy('customerid');
			}
			else {
				$customers = ArrayHelper::getColumn(UserHasCustomer::find()->where(['userid'=>Yii::$app->user->id])->asArray()->all(), 'customerid');
				$_find_projects = Customer::find()->select('lv_customers.*')->join('INNER JOIN', 'lv_user_has_customer', 'lv_user_has_customer.customerid =lv_customers.id')
									->innerJoin('lv_users', 'lv_users.id =lv_user_has_customer.userid')
									->where(['lv_customers.id'=>$customers])
									->groupBy('customerid');				
			}
								
			$deleted_user_count = User::find()->where(['deleted' => 1])->count();
			
            return $this->render('index', [
            		'departments' => $departments,
            		'usertypes' => $usertypes,
            		'_find_projects' => $_find_projects,
					'deleted_user_count' => $deleted_user_count
            	]);
        //}
    	}
               
    }
	
	/**
	* Get deleted users
	* @return mixed
	*/
	public function actionGetdeleted(){
		if(Yii::$app->user->identity->usertype != 1)
		{
				$query = User::find()->where(['deleted' => 1])->orderBy(['firstname' => SORT_ASC, 'lastname' => SORT_ASC]);
		} else {
				$customers = ArrayHelper::getColumn(UserHasCustomer::find()->where(['userid'=>Yii::$app->user->id])->asArray()->all(), 'customerid');										
				$query = User::find()
						->innerJoin('lv_user_has_customer', '`lv_user_has_customer`.`userid` = `lv_users`.`id`')
						->where(['`lv_user_has_customer`.`customerid`'=>$customers, 'deleted' => 1])->orderBy(['firstname' => SORT_ASC, 'lastname' => SORT_ASC]);
		}
		
		$dataProvider = new ActiveDataProvider([
			'query' => $query,
			'pagination' => ['pageSize' => 10],
			]);
		 
		$html = $this->renderPartial('_deleted', [
				'dataProvider' => $dataProvider,
				]);
		
		$_retArray = array('success' => true, 'html' => $html, 'total' => $query->count());
		
		Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
		//return view
		return $_retArray;
		exit();
	}
	
    public function actionProfile(){
        $model = $this->findModel(Yii::$app->user->id);
        if(empty($model->default_ip_address)){
            $model->default_ip_address = Yii::$app->getRequest()->getUserIP();
            $model->save();
        }
        $profile_image = Yii::getAlias('@web').self::FILE_UPLOAD_PATH_USER.'default.jpg';
        if($model->picture_id){
            $mediaModel = Medias::findOne($model->picture_id);
            if(file_exists(Yii::getAlias('@webroot').'/'.$mediaModel->path.$mediaModel->filename)){
                $profile_image = Yii::getAlias('@web').$mediaModel->path.$mediaModel->filename;
            }
        }
        return $this->render('profile', [
            		'model' => $model,
                        'profile_image' => $profile_image
            	]);
    }
    
    public function actionLoad()
    {
    
    	if (Yii::$app->request->isAjax) {
    		$_retArray = array('success' => FALSE, 'html' => '');
			
			if(Yii::$app->user->identity->usertype != 1)
			{
				$query = User::find()->where(['deleted' => 0]);
			} else {
				$customers = ArrayHelper::getColumn(UserHasCustomer::find()->where(['userid'=>Yii::$app->user->id])->asArray()->all(), 'customerid');
				$query = User::find()
							->innerJoin('lv_user_has_customer', '`lv_user_has_customer`.`userid` = `lv_users`.`id`')
							->where(['`lv_user_has_customer`.`customerid`'=>$customers, 'deleted' => 0]);
			}
    		
    		$dataProvider = new ActiveDataProvider([
				'query' => $query,
				'pagination' => ['pageSize' => 10],
				'sort' => ['defaultOrder'=>'firstname asc, lastname asc']
    		]);
    		 
    		$html = $this->renderPartial('_load', [
    			'dataProvider' => $dataProvider,
    		]);
    		
    		$_retArray = array('success' => true, 'html' => $html);
			
    		Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
    		//return view
    		return $_retArray;
    		exit();
    	} else {
    		 
    		throw new NotFoundHttpException('The requested page does not exist.');
    	}
    }
    
    public function actionTload()
    {
    	if (Yii::$app->request->isAjax) {
    
    		$data = Yii::$app->request->get();
    
    		if(Yii::$app->user->identity->usertype != 1)
    		{
    			$query = User::find()->where(['usertype'=>$data['type'], 'deleted' => 0]);
    		} else {
    			$customers = ArrayHelper::getColumn(UserHasCustomer::find()->where(['userid'=>Yii::$app->user->id])->asArray()->all(), 'customerid');
    			$query = User::find()
    			->innerJoin('lv_user_has_customer', '`lv_user_has_customer`.`userid` = `lv_users`.`id`')
    			->where(['`lv_user_has_customer`.`customerid`'=>$customers, 'usertype'=>$data['type'], 'deleted' => 0]);
    		}    		 
    		$dataProvider = new ActiveDataProvider([
    				'query' => $query,
    				'pagination' => ['pageSize' => 15],
    				]); 
    		 
    		echo $this->renderPartial('@app/modules/Users/views/default/_load', [
    				'dataProvider' => $dataProvider,
    				]);
    		exit();
    	} else {
    		 
    		throw new NotFoundHttpException('The requested page does not exist.');
    	}
    }
    
    public function actionLoaduser()
    {
    	if (Yii::$app->request->isAjax) {
    
    		$data = Yii::$app->request->get();
    
    		$query = User::find()->join('INNER JOIN', 'lv_user_has_customer', 'lv_user_has_customer.userid = lv_users.id')->where(['lv_user_has_customer.customerid'=>$data['id']]);
    		 
    		$dataProvider = new ActiveDataProvider([
    				'query' => $query,
    				'pagination' => ['pageSize' => 15],
    				]); 
    		 
    		echo $this->renderPartial('@app/modules/Users/views/default/_load', [
    				'dataProvider' => $dataProvider,
    				]);
    		exit();
    	} else {
    		 
    		throw new NotFoundHttpException('The requested page does not exist.');
    	}
    }
    
    public function actionSearch()
    {
    	$_post = Yii::$app->request->get();
    	 
    	if (Yii::$app->request->isAjax) {
    		$_retArray = array('success' => FALSE, 'html' => '');
    		if (!isset($_post['query'])) {
    			$_retArray = array('success' => FALSE, 'html' => 'Something is wrong! Plese try again!');
    			echo json_encode($_retArray);
    			exit();
    		}
    
    		$query = $_post['query'];
    
    		$searchModel = new UserSearch();
    		$dataProvider = $searchModel->search(['UserSearch'=>['email'=>$query, 'username'=>$query, 'firstname'=>$query, 'lastname'=>$query]]);
    	  
    		$html = $this->renderPartial('_load', [
    				'dataProvider' => $dataProvider,
    				]);
    
    		$_retArray = array('success' => true, 'html' => $html, 'count'=>$dataProvider->getTotalCount());
    		echo json_encode($_retArray);
    		exit();
    	} else {
    		 
    		throw new NotFoundHttpException('The requested page does not exist.');
    	}
    }

    /**
     * 
     * @return type
     * @throws NotFoundHttpException
     */
    public function actionCreatedepartment() {

        if (Yii::$app->request->isAjax) {

            /* Only when Ajax request is cretaed for loading department creation form * */
            $department = array();
            $html = $this->renderAjax('createdepartment', [
                'department' => $department
            ]);

            $_retArray = array('success' => true, 'html' => $html);
            echo json_encode($_retArray);
            exit();
        } else if (isset($_POST['departmentName']) && !empty($_POST['departmentName'])) {

            if (isset($_POST['departmentName'])) {

                $modelDepartment = new Department();
                $modelDepartment->name = $_POST['departmentName'];
                if ($modelDepartment->validate()) {
                    $modelDepartment->save();
                    $_message = 'Department has been created successfully!';
                    Yii::$app->getSession()->setFlash('success', $_message);
                } else {
                    $errors = $modelUser->errors;
                    $_message = json_encode($errors);
                    Yii::$app->getSession()->setFlash('danger', $_message);
                }
                return $this->redirect('index');
            } else {

                throw new NotFoundHttpException('The requested page does not exist.');
            }
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
    
    /**
     * 
     * @return type
     * @throws NotFoundHttpException
     */
    public function actionEditdepartment() {

        if (Yii::$app->request->isAjax) {

            /* Only when Ajax request is cretaed for loading department update form * */
            
            if (isset($_GET['id']) && $_GET['id'] > 0) {
                $_d_id = $_GET['id'];
                $department = Department::findOne($_d_id);
                $html = $this->renderAjax('createdepartment', [
                    'department' => $department
                ]);

            } else {

                $html = "Something is wrong! Please try again.";
            }
            $_retArray = array('success' => true, 'html' => $html);
            echo json_encode($_retArray);
            exit();
        } else if (isset($_POST['departmentName']) && !empty($_POST['departmentName'])) {

            if (isset($_POST['departmentId'])) {

                $_d_id = $_POST['departmentId'];
                $modelDepartment = Department::findOne($_d_id);
                $modelDepartment->name = $_POST['departmentName'];
                if ($modelDepartment->validate()) {
                    $modelDepartment->save();
                    $_message = 'Department has been updated successfully!';
                    Yii::$app->getSession()->setFlash('success', $_message);
                } else {
                    $errors = $modelUser->errors;
                    $_message = json_encode($errors);
                    Yii::$app->getSession()->setFlash('danger', $_message);
                }
                return $this->redirect('index');
            } else {

                throw new NotFoundHttpException('The requested page does not exist.');
            }
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    /**
     * 
     * @return type
     * @throws NotFoundHttpException
     */
    public function actionCreate() {
    	$data = Yii::$app->request->post();
    	
    	$model = new UserCreate;
    	
    	if(empty($data)){
    		$profile_image = null;
    		/*$profile_image = Yii::getAlias('@web').'/public/images/users/default.jpg';
    		$user = $this->findModel(Yii::$app->user->id);
    		if($user->picture_id){
    			$mediaModel = Medias::findOne($user->picture_id);
    			if(file_exists(Yii::getAlias('@webroot').'/'.$mediaModel->path.$mediaModel->filename)){
    				$profile_image = Yii::getAlias('@web').$mediaModel->path.$mediaModel->filename;
    			}
    		}   */ 		
            $departments = Department::find()->all();
            return $this->render('create', [
                'user' => $model,
                'departments'=>$departments,
				'customers'=>array(),
            	'profile_image'=>$profile_image
            ]);

        } else {
			//var_dump($data);
			//exit(1);
                $model->firstname = $_POST['u_firstname'];
                $model->lastname = $_POST['u_lastname'];
                $model->username = $_POST['u_username'];
                $model->email = $_POST['u_email'];
				if(!empty($_POST['u_password']))
				{
					$_trim_val = trim($_POST['u_password']);
					$model->hash_password = md5($_trim_val);   
				}
                $model->usertype = $_POST['u_usertype'];
                if($model->usertype == 3){
                    $model->department = $_POST['u_department'];
                }
                if (isset($_FILES["u_logo"]['name']) && !empty($_FILES["u_logo"]['name'])) {
                    $_result = $this->uploadMedia($_FILES["u_logo"], 'image');
                    if (is_array($_result)) {
                        $_uploaded_file_name = $_result['filename'];
                        $media = new Medias();
                        $media->filename = $_uploaded_file_name;
                        $media->path = self::FILE_UPLOAD_PATH_USER;
                        $media->type = 1;
                        $media->save(); 
                        $model->picture_id = $media->id;
                    }
                }
                
                if ($model->save()) {
					if(Yii::$app->user->identity->usertype!==User::TYPE_CUSTOMER)
						$customers = $_POST['customers'];
					else 
						$customers = ArrayHelper::getColumn(UserHasCustomer::find()->where(['userid'=>Yii::$app->user->id])->asArray()->all(), 'customerid');
					//var_dump($customers);
					if(($model->usertype == 1 || $model->usertype == 9) && !empty($customers) && is_array($customers))
					{
						foreach($customers as $customer)
						{
							$user_has_customer = new UserHasCustomer;
							$user_has_customer->userid = $model->id;
							$user_has_customer->customerid = $customer;
							$user_has_customer->save();
						}
					}
                    $_message = 'User has been created successfully!';
                    Yii::$app->getSession()->setFlash('success', $_message);
                } else {
                    $_message = json_encode($model->errors);
                    Yii::$app->getSession()->setFlash('danger', $_message);
                }
                return $this->redirect(['index']);
        }
    }

    /**
     * 
     * @return type
     * @throws NotFoundHttpException
     */
    public function actionUpdate($id) {
    	
    	$data = Yii::$app->request->post();
    	
    	$model = $this->findModel($id);
		
		date_default_timezone_set('US/Eastern');
		
		if(Yii::$app->user->identity->usertype==User::REPRESENTATIVE && $id != Yii::$app->user->id)
		{
			$_message = 'This action is not allowed.';
			Yii::$app->getSession()->setFlash('danger', $_message);			
			$redirectUrl = '/users/update?id='.Yii::$app->user->id;
			return $this->redirect([$redirectUrl]);			
		}
    	 
    	if(empty($data)){
    		$profile_image = Yii::getAlias('@web').'/public/images/users/default.jpg';
    		if($model->picture_id){
    			$mediaModel = Medias::findOne($model->picture_id);
    			if(file_exists(Yii::getAlias('@webroot').'/'.$mediaModel->path.$mediaModel->filename)){
    				$profile_image = Yii::getAlias('@web').$mediaModel->path.$mediaModel->filename;
    			}
    		}    		
    		
            $departments = Department::find()->all();
            
            return $this->render('update', [ 
               'user' => $model,
               'departments' => $departments,
			   'customers'=>ArrayHelper::getColumn(UserHasCustomer::find()->where(['userid'=>$model->id])->asArray()->all(), 'customerid'),
               'profile_image'=>$profile_image
            ]);
        } else { 

                $model->firstname = $_POST['u_firstname'];
                $model->lastname = $_POST['u_lastname'];
                $model->username = $_POST['u_username'];
                $model->email = $_POST['u_email'];
				if(!empty($_POST['u_password']))
				{
					$_trim_val = trim($_POST['u_password']);
					$model->hash_password = md5($_trim_val);   
				}
				
				if(!empty($_POST['u_usertype']))
				{
                	$model->usertype = $_POST['u_usertype'];              
	                if($model->usertype == 3 && isset($_POST['u_department'])){
	                    $model->department = $_POST['u_department'];
	                }
				}
                
                $model->modified_at = date('Y-m-d H:i:s');
                
                if (isset($_FILES["u_logo"]['name']) && !empty($_FILES["u_logo"]['name'])) {
                    $_result = $this->uploadMedia($_FILES["u_logo"], 'image');
                    if (is_array($_result)) {
                        $_uploaded_file_name = $_result['filename'];
                        $media = new Medias();
                        if($model->picture_id){
                            $media = Medias::findOne($model->picture_id);
                        }
                        $media->filename = $_uploaded_file_name;
                        $media->path = self::FILE_UPLOAD_PATH_USER;
                        $media->type = 1;
                        $media->save();
                        $model->picture_id = $media->id;
                    }
                }
                if ($model->save()) {
                	$customers = $_POST['customers'];
          			//
                	if(!empty($customers))
                	{
						//remove old entries
						foreach(UserHasCustomer::find()->where(['userid'=>$model->id])->all() as $entry)
						{
							$entry->delete();
						}
						//add new
						if(($model->usertype == 1 || $model->usertype == 9) && !empty($customers) && is_array($customers))
						{
							foreach($customers as $customer)
							{
								$user_has_customer = new UserHasCustomer;
								$user_has_customer->userid = $model->id;
								$user_has_customer->customerid = $customer;
								$user_has_customer->save();
							}
						}
                	}
                    $_message = 'User has been updated successfully!';
                    Yii::$app->getSession()->setFlash('success', $_message);
                } else {
                    $_message = json_encode($model->errors);
                    Yii::$app->getSession()->setFlash('danger', $_message);
                }
                $redirectUrl = 'index';
                if(Yii::$app->user->identity->usertype==User::REPRESENTATIVE)
                    $redirectUrl = '/users/update?id='.$id;
                return $this->redirect([$redirectUrl]);
        }
    }

    /**
     * 
     * @return type
     * @throws NotFoundHttpException
     */
    public function actionDelete() {
        if (isset($_GET['id']))
            $user = $_GET['id'];
        else
            throw new NotFoundHttpException('The requested page does not exist.');
        $this->findModel($user)->delete();
        return $this->redirect(['default/index']);
    }
	
     /**
     * Soft delete the existing User.
     * @param $id integer
     * @return mixed
     */    
    public function actionSdelete($id){
        $model = Users::findOne($id);
        $model->deleted = 1;
        if($model->save(false)){
                $_message = 'User has been deleted successfully!';
                Yii::$app->getSession()->setFlash('danger', $_message);      
            } else {
                $_message = 'There is a problem in deleting User!';
                Yii::$app->getSession()->setFlash('warning', $_message);     
            }
        return $this->redirect(['index']);
    }
    
    /**
	* Revert deletion of an existing User model.
	* @param integer $id
	* @return boolean
	*/
	public function actionRevert($id){
		$model = Users::findOne($id);
		$model->deleted = 0;
		$model->save(false);
		if(Yii::$app->user->identity->usertype != 1)
			{
					$query = User::find()->where(['deleted' => 1]);
			} else {
					$customers = ArrayHelper::getColumn(UserHasCustomer::find()->where(['userid'=>Yii::$app->user->id])->asArray()->all(), 'customerid');										
					$query = User::find()
							->innerJoin('lv_user_has_customer', '`lv_user_has_customer`.`userid` = `lv_users`.`id`')
							->where(['`lv_user_has_customer`.`customerid`'=>$customers, 'deleted' => 1]);
			}
		return $query->count();
	}

    
    /**
     * 
     * @throws NotFoundHttpException
     */
    public function actionView() {

        if (Yii::$app->request->isAjax) {

            $_retArray = array('success' => FALSE, 'html' => '');
            $_post = Yii::$app->request->get();
            if (!isset($_post['id'])) {
                $_retArray = array('success' => FALSE, 'html' => 'Something is wrong! Plese try again!');
                echo json_encode($_retArray);
                exit();
            }
            $id = $_post['id'];
            $find = $this->findModel($id);
            $profile_image = Yii::getAlias('@web').self::FILE_UPLOAD_PATH_USER.'default.jpg';
            if($find->picture_id){
                $mediaModel = Medias::findOne($find->picture_id);
                if(file_exists(Yii::getAlias('@webroot').'/'.$mediaModel->path.$mediaModel->filename)){
                    $profile_image = Yii::getAlias('@web').$mediaModel->path.$mediaModel->filename;
                }
            }
            $html = $this->renderAjax('view', [
                'model' => $find,
                'profile_image' => $profile_image
            ]);
            $_retArray = array('success' => true, 'html' => $html);
            echo json_encode($_retArray);
            exit();
        } else {

            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
 
    /**
     * 
     * @throws NotFoundHttpException
     */
    
    public function actionShowdepartments() {

        if (Yii::$app->request->isAjax) {
            $_retArray = array('success' => FALSE, 'html' => '');
            $find = Department::find()->all();
            $html = $this->renderAjax('showAllDepartments', [
                'model' => $find
            ]);
            $_retArray = array('success' => true, 'html' => $html);
            echo json_encode($_retArray);
            exit();
        } else {

            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    /**
     * 
     * @param type $_upoload_file
     * @param type $_type
     * @return boolean|string
     */
    private function uploadMedia($_upoload_file, $_type = '') {

        /***
         * to create folders if not folders are not available
         */
        
        $new_path = Yii::getAlias('@webroot') . self::FILE_UPLOAD_PATH;
        if (!is_dir($new_path)) {
            mkdir($new_path, 0777, true);
        }

        
        $new_path = Yii::getAlias('@webroot') . self::FILE_UPLOAD_PATH_USER;
        if (!is_dir($new_path)) {
            mkdir($new_path, 0777, true);
        }

        
        $_error = "";
        $target_dir = Yii::getAlias('@webroot') . self::FILE_UPLOAD_PATH_USER;
        $target_file = $target_dir . basename($_upoload_file["name"]);
        $uploadOk = 1;
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
        if ($_type == "image") {
            $check = getimagesize($_upoload_file["tmp_name"]);
            if ($check !== false) {
                $uploadOk = 1;
            } else {
                return $_error = $_error . "File is not an image.";
            }
        }

        if ($_upoload_file["size"] > 2000000) {
            return $_error = $_error . "Sorry, your file is too large.";
        }
        if ($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif") {
            return $_error = $_error . "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
        }
        if (1) {

            $temp = explode(".", $_upoload_file["name"]);
            $newfilename = round(microtime(true)) . '.' . end($temp);
            if (move_uploaded_file($_upoload_file["tmp_name"], $target_dir . $newfilename)) {

                $_success['success'] = true;
                $_success['filename'] = $newfilename;
                return $_success;
            } else {

                return $_error = $_error . "Sorry, there was an error uploading your file.";
            }
        }
        return false;
    }
    
    /**
     * 
     * @param type $id
     * @return type
     * @throws NotFoundHttpException
     */
    protected function findModel($id) {

        if (($model = UserCreate::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

}