<?php

namespace app\controllers;
use Yii;
use app\models\Purchase;
use app\models\Category;
use app\models\Customer;
use app\models\Location;
use app\models\Medias;
use app\models\ModelOption;
use app\models\Models;
use app\models\ModelAssembly;
use app\models\Manufacturer;
use app\models\ItemRequested;
use app\models\Itemspurchased;
use app\models\Department;
use app\models\Itemsordered;
use app\models\Item;
use app\models\Itemlog;
use app\models\Partnumber;
use app\models\QOrder;
use app\modules\Orders\models\Order;
use app\models\CustomerSetting;
use app\models\QItemsordered;
use app\models\SystemSetting;
use app\models\Users;
use app\models\ModelsPicture;
use app\models\Vendor;
use app\vendor\Uploader;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use app\models\User;
use app\models\UserHasCustomer;
use yii\web\Session;
use yii\web\Cookie;
use yii\db\Query;
use app\models\ShipmentMethod;
use app\models\ShippingCompany;
use app\models\Itemstesting;

class AjaxrequestController extends \yii\web\Controller
{
	//Errors constants
	//---> Common errors 
	const FIRSTNAME_ERROR = "Firstname field required !";
	const LASTNAME_ERROR = "Lastname field required !";
	const EMAIL_ERROR = "Email field required !";
	const NOT_VALID_EMAIL_ERROR = "Not a valid e-mail address";
	//---> Customer errors
	const CUST_COMPANY_ERROR = "Company name field required !";
	const CUST_GEN_ADDRESS_ERROR = "Address field required !";
	const CUST_GEN_COUNTRY_ERROR = "Country field required !";
	const CUST_GEN_CITY_ERROR = "City field required !";
	const CUST_GEN_STATE_ERROR = "State field required !";
	const CUST_GEN_ZIP_ERROR = "Zip field required !";	
	const CUST_SHIPPING_ADDRESS_ERROR = "Shipping Address field required !";
	const CUST_SHIPPING_COUNTRY_ERROR = "Shipping Country field required !";
	const CUST_SHIPPING_CITY_ERROR = "Shipping City field required !";
	const CUST_SHIPPING_STATE_ERROR = "Shipping State field required !";
	const CUST_SHIPPING_ZIP_ERROR = "Shipping Zip field required !";
	const CUST_BILLING_ADDRESS_ERROR = "Billing Address field required !";
	const CUST_BILLING_COUNTRY_ERROR = "Billing Country field required !";
	const CUST_BILLING_CITY_ERROR = "Billing City field required !";
	const CUST_BILLING_STATE_ERROR = "Billing State field required !";
	const CUST_BILLING_ZIP_ERROR = "Billing Zip field required !";
	const CUST_ITEM_DESCRIPTION = "Description of this item is required !";
	const CUST_ITEM_MANPART = "Manufacturer part number is required !";
	const CUST_DEFAULT_ACCOUNT_NUMBER_ERROR = "Default Customer Account Number required !";
	const CUST_DEFAULT_SHIPPING_METHOD_ERROR = "Default Customer Shipping Method required !";
	
	protected $_items_status = array(null, 'In Transit', 'Received', 'In Stock', 'Picked', 'In Progress', 'Ready to ship', 'Shipped', 'Ready to Invoice', 'Invoiced', 'Complete');
	
    public function actionAddcustomer()
    {
		$data = array();
		$message = array();
		$errors = array();
		$data = Yii::$app->request->post();
		//few vars defined.
		$moreCustomerAddressInfo = $data['moreCustomerAddressInfo'];
		/*$data['company_name'] = "test";
		$data['email'] = "test@gmail.com";
		$data['shipping_address'] = "test";
		$data['shipping_country'] = "test";
		$data['shipping_city'] = "test";
		$data['shipping_state'] = "test";
		$data['shipping_zip'] = "test";
		$data['firstname'] = "test";
		$data['lastname'] = "test";
		$data['defaultaccountnumber'] = "test";
		$data['defaultshippingmethod'] = "test";*/
		$require_order = $data['require_order'];
		$require_serial_number = $data['require_serial_number'];
		$receivingLocation = $data['receiving_location'];
		//
		$require_order = ($require_order == 'true') ? 1: 0;
		$require_serial_number = ($require_serial_number == 'true') ? 1: 0;
		//$today = date("Y-m-d H:i:s");		
		$time = new \DateTime('now', new \DateTimeZone('EST'));	
		$today = $time->format('Y-m-d H:i:s');	
		$picture_uploaded = (isset($_COOKIE['__user_picture'])) ? $_COOKIE['__user_picture'] : null;
		//data validation.
		//general data validation.
		if(empty($data['firstname'])) $errors['efirstname']= self::FIRSTNAME_ERROR;
		if(empty($data['lastname'])) $errors['elastname']= self::LASTNAME_ERROR;
		if(empty($data['company_name'])) $errors['ecompanyname']= self::CUST_COMPANY_ERROR;
		if(empty($data['defaultaccountnumber'])) $errors['edefaultaccountnumber']=self::CUST_DEFAULT_ACCOUNT_NUMBER_ERROR;
		if(empty($data['defaultshippingmethod'])) $errors['edefaultshippingmethod']=self::CUST_DEFAULT_SHIPPING_METHOD_ERROR;
		if(empty($data['email'])) $errors['eemail']=self::EMAIL_ERROR;
		if(!empty($data['email']) && !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) $errors['eemail']=self::NOT_VALID_EMAIL_ERROR;
		//Shipping data validation.
		if(empty($data['shipping_address'])) $errors['eshippingaddress']=self::CUST_SHIPPING_ADDRESS_ERROR;
		if(empty($data['shipping_country'])) $errors['eshippingcountry']=self::CUST_SHIPPING_COUNTRY_ERROR;
		if(empty($data['shipping_city'])) $errors['eshippingcity']=self::CUST_SHIPPING_CITY_ERROR;
		if(empty($data['shipping_state'])) $errors['eshippingstate']=self::CUST_SHIPPING_STATE_ERROR;
		if(empty($data['shipping_zip'])) $errors['eshippingzip']=self::CUST_SHIPPING_ZIP_ERROR;
		
		if($moreCustomerAddressInfo)
		{
			//Billing data validation.
			if(empty($data['billing_address'])) $errors['ebillingaddress']=self::CUST_BILLING_ADDRESS_ERROR;
			if(empty($data['billing_country'])) $errors['ebillingcountry']=self::CUST_BILLING_COUNTRY_ERROR;
			if(empty($data['billing_city'])) $errors['ebillingcity']=self::CUST_BILLING_CITY_ERROR;
			if(empty($data['billing_state'])) $errors['ebillingstate']=self::CUST_BILLING_STATE_ERROR;
			if(empty($data['billing_zip'])) $errors['ebillingzip']=self::CUST_BILLING_ZIP_ERROR;
		}
		//return response + action
		if ( ! empty($errors)) {
			// if there are items in our errors array, return those errors
			$message['success'] = false;
			$message['errors']  = $errors;
		} else {
			$billinglocation = null;
			$billing_address = (isset($data['billing_address'])) ? $data['billing_address'] : null;
			$billing_country = (isset($data['billing_country'])) ? $data['billing_country'] : null;
			$billing_city = (isset($data['billing_city'])) ? $data['billing_city'] : null;
			$billing_state = (isset($data['billing_state'])) ? $data['billing_state'] : null;
			$billing_zip = (isset($data['billing_zip'])) ? $data['billing_zip'] : null;
			
			$media_id = null;
			$session = Yii::$app->session;
			if ($session->has('__user_picture'))
			{
				$media = new Medias;
				$media->filename = $_SESSION['__user_picture'];
				$media->path = "/images/customers";
				$media->save();
				$media_id = $media->id;
			}
			
			//save customer 
			$query = "INSERT INTO {{%customers}} (companyname, firstname, lastname, owner_id, phone, email, trackincomingserials, requireordernumber, picture_id, defaultreceivinglocation, created_at)
				 VALUES(:company, :firstname, :lastname, :owner_id, :phone, :email, :trackincomingserials, :requireordernumber, :image, :defaultreceivinglocation, :created_at);";
			Yii::$app->db->createCommand($query)
				->bindValue(':company', $data['company_name'])
				->bindValue(':firstname', $data['firstname'])
				->bindValue(':lastname', $data['lastname'])
				->bindValue(':owner_id', Yii::$app->user->id)
				->bindValue(':phone', $data['phone'])
				->bindValue(':email', $data['email'])
				->bindValue(':trackincomingserials', $require_serial_number)
				->bindValue(':requireordernumber', $require_order)
				->bindValue(':image', $media_id)
				->bindValue(':defaultreceivinglocation', $receivingLocation)
				->bindValue(':created_at', $today)
				->execute();
				
			$customerId = Yii::$app->db->getLastInsertID();
			
			$customerSetting = new CustomerSetting;
			$customerSetting->customerid = $customerId;
			$customerSetting->default_account_number = $data['defaultaccountnumber'];
			$customerSetting->default_shipping_method = $data['defaultshippingmethod'];
			$customerSetting->save();
			//
			$query = "INSERT INTO {{%locations}} (	customer_id, storenum, address, country, city, state, zipcode, phone, email) VALUES(:customer, :storenum, :address, :country, :city, :state, :zipcode, :phone, :email);";
			
			Yii::$app->db->createCommand($query)
				->bindValue(':customer', $customerId)
				->bindValue(':storenum', null)
				->bindValue(':address', $data['shipping_address'])
				->bindValue(':country', $data['shipping_country'])
				->bindValue(':city', $data['shipping_city'])
				->bindValue(':state', $data['shipping_state'])
				->bindValue(':zipcode', $data['shipping_zip'])
				->bindValue(':phone', $data['phone'])
				->bindValue(':email', $data['email'])
				->execute();
				
			$shippinglocation = Yii::$app->db->getLastInsertID();
			//
			if($moreCustomerAddressInfo)
			{
			$query = "INSERT INTO {{%locations}} (	customer_id, storenum, address, country, city, state, zipcode, phone, email) VALUES(:customer, :storenum, :address, :country, :city, :state, :zipcode, :phone, :email);";
				
				Yii::$app->db->createCommand($query)
					->bindValue(':customer', $customerId)
					->bindValue(':storenum', null)
					->bindValue(':address', $data['billing_address'])
					->bindValue(':country', $data['billing_country'])
					->bindValue(':city', $data['billing_city'])
					->bindValue(':state', $data['billing_state'])
					->bindValue(':zipcode', $data['billing_zip'])
					->bindValue(':phone', $data['phone'])
					->bindValue(':email', $data['email'])
					->execute();
					
				$billinglocation = Yii::$app->db->getLastInsertID();
			}
			//update shipping and billing details on customer instance
			$update_query = "UPDATE {{%customers}} SET defaultshippinglocation=:defaultshippinglocation, defaultbillinglocation=:defaultbillinglocation WHERE id=:id";
			Yii::$app->db->createCommand($update_query)
				->bindValue(':defaultshippinglocation', $shippinglocation)
				->bindValue(':defaultbillinglocation', $billinglocation)
				->bindValue(':id', $customerId)			
				->execute();
			// show a message of success and provide a true success variable
			$message['success'] = true;
			$message['message'] = "Customer <b>$data[email]</b> is succefully registered!";			
		}
		//echo \yii\helpers\Json::encode($message);    
		Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;		
		return $message;
    }
	
	public function actionAddlocation()
	{
		$data = array();
		$message = array();
		$errors = array();
		$data = Yii::$app->request->post();
		//$data['customerid'] = 4; 
		$customer_id = (!empty($data['customerid'])) ? $data['customerid'] : 0;
		$storenum = (isset($data['storenum'])) ? $data['storenum'] : Location::generateUniqueStoreNum();
		//general data validation.
		if(!empty($data['email']) && !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) $errors['eemail']=self::NOT_VALID_EMAIL_ERROR; 
		/*$data['address'] = 'test';
		$data['country'] = 'test';
		$data['city'] = 'test';
		$data['state'] = 'test';
		$data['zip'] = 'test';*/
		//$data['lshippingmethod'] = 5;
		//$data['laccountnumber'] = 4;
		$data['lshippingmethod'] = (isset($data['lshippingmethod'])) ? $data['lshippingmethod'] : 0;
		$data['laccountnumber'] = isset($data['laccountnumber']) ? $data['laccountnumber'] : "";
		//Shipping data validation.
		if(empty($data['address'])) $errors['eaddress']=self::CUST_GEN_ADDRESS_ERROR;
		if(empty($data['country'])) $errors['ecountry']=self::CUST_GEN_COUNTRY_ERROR;
		if(empty($data['city'])) $errors['ecity']=self::CUST_GEN_CITY_ERROR;
		if(empty($data['state'])) $errors['estate']=self::CUST_GEN_STATE_ERROR;
		if(empty($data['zip'])) $errors['ezip']=self::CUST_GEN_ZIP_ERROR;
		//return response + action
		if ( ! empty($errors)) {
			// if there are items in our errors array, return those errors
			$message['success'] = false;
			$message['errors']  = $errors;
		} else {
			//save location
			$query = "INSERT INTO {{%locations}} (customer_id, storename, storenum, address, address2, country, city, state, zipcode, phone, email, shipping_deliverymethod, default_accountnumber) VALUES(:customer, :storename, :storenum, :address, :address2, :country, :city, :state, :zipcode, :phone, :email, :shipping_deliverymethod, :default_accountnumber);";
			
			Yii::$app->db->createCommand($query)
				->bindValue(':customer', $customer_id)	
				->bindValue(':storename', $data['storename'])
				->bindValue(':storenum', $storenum)
				->bindValue(':address', $data['address'])
				->bindValue(':address2', $data['address2'])
				->bindValue(':country', $data['country'])
				->bindValue(':city', $data['city'])
				->bindValue(':state', $data['state'])
				->bindValue(':zipcode', $data['zip'])
				->bindValue(':phone', $data['phone'])
				->bindValue(':email', $data['email'])
				->bindValue(':shipping_deliverymethod', $data['lshippingmethod'])
				->bindValue(':default_accountnumber', $data['laccountnumber'])
				->execute();
			// show a message of success and provide a true success variable
			$message['success'] = true;
			$message['message'] = "Location is successfully registered!";			
			$message['id'] = Yii::$app->db->getLastInsertID();			
		}
		//echo \yii\helpers\Json::encode($message);    
		Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;		
		return $message;		
	}
	
	public function actionAddnewitemrequest()
	{
		$data = array();
		$message = array();
		$errors = array();
		$data = Yii::$app->request->post();
		//$custome_id = ;
		//validation 
		if(empty($data['rdescription'])) $errors['edescription']=self::CUST_ITEM_DESCRIPTION;
		if(empty($data['rmanpartnum'])) $errors['emanpart']=self::CUST_ITEM_MANPART;
		//return response + action
		if ( ! empty($errors)) {
			// if there are items in our errors array, return those errors
			$message['success'] = false;
			$message['errors']  = $errors;
		} else {
			//save location
			$model = new ItemRequested;
			$model->description = $data['rdescription'];
			$model->manpartnum = $data['rmanpartnum'];
			$model->customer_id = $data['rcustomer'];
			$model->save();
			// show a message of success and provide a true success variable
			$message['success'] = true;
			$message['message'] = "We are succefully received your request!";			
		}
		//echo \yii\helpers\Json::encode($message);    
		Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;		
		return $message;
	}
	
	public function actionGetmaincustomersettings()
	{
		$setting= SystemSetting::find()->one();
		 
		$output = array();
		
		$suggestions = array();
		
		$suggestions['accountnumber'] = $setting['account_number'];  
		
		$suggestions['shippingcompany'] =  ShipmentMethod::findOne($setting['shipping_method'])->shipping_company_id;  
		
		$suggestions['shippingmethod'] = $setting['shipping_method'];

		$output[]=$suggestions;
		
		Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
		//return list
		return $output;		
	}

	public function actionVerifycustomer($customer)
	{
		$cust= Customer::find()->where(['companyname'=>$customer_name])->one();
		$message = array();
		if(empty($cust))
			$message['error'] = true;
		else 
			$message['success'] = true;
		
		Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
		
		return $message;
	}
	
/*	public function actionListorderlocations($customer)
	{
		$customer_name = $customer;
		
		if(!empty($customer_name))
		{
			$cust= Customer::find()->where(['companyname'=>$customer_name])->one();
			if(!empty($cust))
				$locations = Location::find()->where(['customer_id'=>$cust->id])->all();
			else 
				$locations = array();
		}
		else 
			$locations = Location::find()->all();
		
		$_output = array();
		
		$suggestions = array();
		
		foreach($locations as $location)
		{
			$output = "";
			if(!empty($location->storenum))
				$output .= "Store#: " . $location->storenum . " - ";
			if(!empty($location->storename))
				$output .= $location->storename  . ' - ';
			//
			$output .= $location->address . " " . $location->city . " " . $location->state . " " . $location->zipcode;
			$suggestions[]=array('id' => $location->id, 'name' => $output);
		}
		//
		$_output = $suggestions;
		
		Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
		//return list
		return $_output;
	}*/
	public function actionListorderlocations($customer)
	{
		//$customer_name = $customer;
	
		if(!empty($customer))
		{
			//$cust= Customer::find()->where(['companyname'=>$customer])->one();
			$cust= Customer::findOne($customer);
			if(!empty($cust))
				$locations = Location::find()->where(['customer_id'=>$cust->id])->all();
			else
				$locations = array();
		}
		else
			$locations = Location::find()->all();
	
		$_output = array();
	
		$suggestions = array();
	
		foreach($locations as $location)
		{
			$output = "";
			if(!empty($location->storenum))
				$output .= "Store#: " . $location->storenum . " - ";
			if(!empty($location->storename))
				$output .= $location->storename  . ' - ';
			//
			$output .= $location->address . " " . $location->address2 . " " . $location->city . " " . $location->state . " " . $location->zipcode;
			$suggestions[]=array('id' => $location->id, 'name' => $output);
		} 
		//
		$_output = $suggestions;
	 
		Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
		//return list
		return $_output; 
	}
	
	public function actionListshippingmethods($company)
	{
		$_output = array();
	 
		$suggestions = array();
		
		if($company==1)
		{
			foreach(ShipmentMethod::find()->where(['like', 'code', ShippingCompany::findOne($company)->name])->all() as $model)
			{
				$ups = new \Ups\Entity\Service;
				$ups->setCode($model->_value);
				$suggestions[]=array('id' => $model->id, 'name' => $ups->getName());
			}
		}
		else if($company==3) //Waiting DHL issues solved
		{}
		else 
		{
			foreach(ShipmentMethod::find()->where(['like', 'code', ShippingCompany::findOne($company)->name])->all() as $model)
			{
				$suggestions[]=array('id' => $model->id, 'name' => $model->_value);
			}			
		}
		//
		$_output = $suggestions;
	 
		Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
		//return list
		return $_output; 
	}
	
	public function actionGetshippingmaindetails($customer)
	{
		$_output = array();
	 		
		$customerSetting = CustomerSetting::findOne($customer);
		
		$_output['s1'] = $customerSetting->default_account_number;
		
		$shippingmethod = ShipmentMethod::findOne($customerSetting->default_shipping_method);
		
		$_shipping_method = $customerSetting->default_shipping_method;
		
		$_output['s2'] = $shippingmethod->shipping_company_id;
		
		$_output['s3'] = $_shipping_method;
		
		Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
		return $_output; 		
	}
	
	public function actionGetshippingchoice($customer)
	{
		$_output = array();
		
		$customer = Customer::findOne($customer);
		
		$_output['choice'] = $customer->defaultshippingchoice;
		
		Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
		return $_output; 	
	}
	
	public function actionGetshippingotherdetails($customer)
	{
		$_output = array();
	 		
		$customerSetting = CustomerSetting::findOne($customer);
		
		$_output['s1'] = $customerSetting->secondary_account_number;
		
		$shippingmethod = ShipmentMethod::findOne($customerSetting->secondary_shipping_method);
		$_shipping_method = $customerSetting->secondary_shipping_method;
		/*if($shippingmethod->shipping_company_id===1)
		{
			$ups = new \Ups\Entity\Service;
			$ups->setCode($shippingmethod->_value);	
			$_shipping_method = $ups->getName();
		}
		else if($company==3) //Waiting DHL issues solved
		{}
		else
		{
			$_shipping_method = $shippingmethod->_value;
		}*/
		
		$_output['s2'] = $shippingmethod->shipping_company_id;
		
		$_output['s3'] = $_shipping_method;
		
		Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
		//return list
		return $_output; 		
	}
	
	public function actionGetshippingassetdetails()
	{
		$_output = array();
	 		
		$assetSetting = SystemSetting::find()->one();
		
		$_output['s1'] = $assetSetting->account_number;
		
		$shippingmethod = ShipmentMethod::findOne($assetSetting->shipping_method);
		$_shipping_method = $assetSetting->shipping_method;
		
		$_output['s2'] = $shippingmethod->shipping_company_id;
		
		$_output['s3'] = $_shipping_method;
		
		Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
		//return list
		return $_output; 		
	}
	
	public function actionGetshippingotherdetailsfromlocation($locationid)
	{
		$_output = array();
	 		
		$location = Location::findOne($locationid);
		
		$_output['s1'] = $location->default_accountnumber;
		
		$shippingmethod = ShipmentMethod::findOne($location->shipping_deliverymethod);
				
		$_output['s2'] = $shippingmethod->shipping_company_id;
		
		$_output['s3'] = $location->shipping_deliverymethod;
		
		Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
		//return list
		return $_output; 		
	}
	
	public function actionListorderitems($customer)
	{
		$customer_name = Customer::findOne($customer)->companyname;
		$models = array(); 
	
		if(!empty($customer_name))
		{
			$cust= Customer::findOne($customer);
			//echo $cust->id;
			//if(!empty($cust))
			//{ 
				$items = Item::find()->where(['customer'=>$cust->id, 'status'=>4, 'assembly'=>0])->join('INNER JOIN', 
					'lv_models',
					'lv_models.id =lv_items.model'
				)->groupBy(['model'])->all();
				$models = ArrayHelper::getColumn($items, 'model');
				//$models=array_unique($models);
				//var_dump($models);exit(1);
			//}
		}

		$_output = array();
	
		$suggestions = array();
		
		$customer_item_qty_item = null;
		
		if($cust!==null)
		{		
			foreach(ModelAssembly::find()->where(['customerid'=>$cust->id])->groupBy('modelid')->all() as $assembly)
			{
				$model = Models::findOne($assembly->modelid);
				$man = Manufacturer::findOne($model->manufacturer);
				$category = Category::findOne($model->category_id)->categoryname;
				$_models = ModelAssembly::find()->where(['customerid'=>$cust->id, 'modelid'=>$assembly->modelid])->all();
				$_count_models = ModelAssembly::find()->where(['customerid'=>$cust->id, 'modelid'=>$assembly->modelid])->count();
				$item_customer_available = 0;
				if(!empty($customer_name))
				{
					foreach($_models as $_model) {
						$item_customer_available += Item::find()->where(['customer'=>$cust->id, 'model'=>$_model->partid, 'status'=>4])->count();
					}
					if($_count_models > 0)
						$item_customer_available = $item_customer_available / $_count_models;
					$item_customer_available = sprintf('%0.2f', round($item_customer_available, 2));
					$customer_item_qty_item =  $item_customer_available . " Available In $customer_name Stock";
				}
				$item = $man->name. ' ' .$model->descrip . ' (' . $customer_item_qty_item . ')';
				if($model->id!==null)
					$suggestions[]=array('id' => $model->id, 'name' => trim($item));			
			}
		
			foreach($models as $model_id) 
			{
				$model = Models::findOne($model_id);
				$man = Manufacturer::findOne($model->manufacturer);
				$category = Category::findOne($model->category_id)->categoryname;
				if(!empty($customer_name))
				{
					$item_customer_available = Item::find()->where(['customer'=>$cust->id, 'model'=>$model_id, 'status'=>array_search('In Stock', Item::$status)])->count();
					$customer_item_qty_item =  $item_customer_available . " Available In $customer_name Stock";
					$reserved_items = Item::find()->where(['customer'=>$cust->id, 'model'=>$model_id, 'status'=>array_search('Reserved', Item::$status)])->count();
					if($cust->trackincomingserials == 1 &&  $model->serialized == 1 && $reserved_items > 0) 
						$customer_item_qty_item .= " " . $reserved_items . " Reserved";
				}
				$item = $man->name. ' ' .$model->descrip . ' (' . $customer_item_qty_item . ') ' . Partnumber::find()->where(['customer'=>$cust->id, 'model'=>$model_id])->one()->partid;
				//$item = (isset(Partnumber::find()->where(['customer'=>$cust->id, 'model'=>$model_id])->one())) ? $item . '(' . Partnumber::find()->where(['customer'=>$cust->id, 'model'=>$model_id])->one()->partid . ')' :  $item . '';
				if($model->id!==null)
					//$suggestions[]=array('id' => $model->id, 'name' => (!empty($category)) ? '(' . $category . ') ' . $item : $item);
					$suggestions[]=array('id' => $model->id, 'name' => trim($item));
			}
		}
		//
		$_output = $suggestions;
	
		Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
		//return list
		return $_output;		
	}
	 
	public function actionVerifyqtystock($model, $qty=0){
		$message = array();
		$errors = array();
		$model = trim($model);
		$qty = (int) $qty;
		//
		$in_stock = Item::find()->where(['model'=>$model, 'status'=>4])->count();
		
		if($qty>$in_stock)
			$message['error'] = true;
		else
		{	
			if(strlen($model)>0 && $in_stock>0)
				$message['success'] = true;
			/*else 
				$message['error'] = true;*/
		}
		//echo \yii\helpers\Json::encode($message);    
		Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;		
		return $message;		
	}
	
	public function actionCheckstockavailable($model, $customer)
	{
		$_output = array();
		
		$suggestions = array();
		
		$in_stock = Item::find()->where(['model'=>$model, 'customer'=>$customer, 'status'=>array_search('In Stock', Item::$status)])->count();
		
		$suggestions['stock']=$in_stock;
		$output[] = $suggestions;
		//
		Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
		//return list
		return $output;		
	}
	
	public function actionUploadinventorypicture()
	{
		$session = Yii::$app->session;
		
		$new_path = Yii::getAlias('@webroot') . "/public/images/models/";
				
		$picture_name = $_FILES['files']['name'];
		
		$ext = pathinfo($picture_name, PATHINFO_EXTENSION);
		
		$uploader = new Uploader();
		
		$data = $uploader->upload($_FILES['files'], array( 
				'limit' => 10, //Maximum Limit of files. {null, Number}
				'maxSize' => 10, //Maximum Size of files {null, Number(in MB's)}
				'extensions' => null, //Whitelist for file extension. {null, Array(ex: array('jpg', 'png'))}
				'required' => false, //Minimum one file is required for upload {Boolean}
				'uploadDir' => $new_path, //Upload directory {String}
				'title' => basename($picture_name, ".".$ext), //New file name {null, String, Array} *please read documentation in README.md
				'removeFiles' => true, //Enable file exclusion {Boolean(extra for jQuery.filer), String($_POST field name containing json data with file names)}
				'perms' => null, //Uploaded file permisions {null, Number}
				'onCheck' => null, //A callback function name to be called by checking a file for errors (must return an array) | ($file) | Callback
				'onError' => null, //A callback function name to be called if an error occured (must return an array) | ($errors, $file) | Callback
				'onSuccess' => null, //A callback function name to be called if all files were successfully uploaded | ($files, $metas) | Callback
				'onUpload' => null, //A callback function name to be called if all files were successfully uploaded (must return an array) | ($file) | Callback
				'onComplete' => null, //A callback function name to be called when upload is complete | ($file) | Callback
				//'onRemove' => 'onFilesRemoveCallback' //A callback function name to be called by removing files (must return an array) | ($removed_files) | Callback
		));
		
		if($data['isComplete']){
			$files = $data['data'];
			$media = new Medias;
			$media->filename = $picture_name;
			$media->path = "models/";
			$media->type = 4;
			$media->save();
			$session['__last_models_picture_id'] = base64_encode($media->id);
			//
			$_modelmedia = new ModelsPicture;
			$_modelmedia->_key = $session['__new_model_key'];
			$_modelmedia->mediaid = $media->id;
			$_modelmedia->save();
			//
			print_r($files);
		}
		
		if($data['hasErrors']){
			$errors = $data['errors'];
			print_r($errors);
		}			
	}
	
	public function actionUploadorderfile()
	{
		$session = Yii::$app->session;
		
		$new_path = Yii::getAlias('@webroot') . "/uploads/orders/";
		if (!is_dir($new_path)) {
			mkdir($new_path, 0777, true);
		}
		
		$path = $_FILES['files']['name'];
		$ext = pathinfo($path, PATHINFO_EXTENSION);
		
		$picture_name = md5(uniqid(true));
		
		$uploader = new Uploader();
		$data = $uploader->upload($_FILES['files'], array(
				'limit' => 10, //Maximum Limit of files. {null, Number}
				'maxSize' => 10, //Maximum Size of files {null, Number(in MB's)}
				'extensions' => null, //Whitelist for file extension. {null, Array(ex: array('jpg', 'png'))}
				'required' => false, //Minimum one file is required for upload {Boolean}
				'uploadDir' => $new_path, //Upload directory {String}
				'title' => $picture_name, //New file name {null, String, Array} *please read documentation in README.md
				'removeFiles' => true, //Enable file exclusion {Boolean(extra for jQuery.filer), String($_POST field name containing json data with file names)}
				'perms' => null, //Uploaded file permisions {null, Number}
				'onCheck' => null, //A callback function name to be called by checking a file for errors (must return an array) | ($file) | Callback
				'onError' => null, //A callback function name to be called if an error occured (must return an array) | ($errors, $file) | Callback
				'onSuccess' => null, //A callback function name to be called if all files were successfully uploaded | ($files, $metas) | Callback
				'onUpload' => null, //A callback function name to be called if all files were successfully uploaded (must return an array) | ($file) | Callback
				'onComplete' => null, //A callback function name to be called when upload is complete | ($file) | Callback
				'onRemove' => 'onFilesRemoveCallback' //A callback function name to be called by removing files (must return an array) | ($removed_files) | Callback
		));
		
		if($data['isComplete']){
			$files = $data['data'];
			$media = new Medias;
			$media->filename = $picture_name . "." . $ext;
			$media->path = "orders/";
			$media->type = 4;
			$media->save();
			//
			$session->set('__order_picture_id', base64_encode($media->id));
			print_r($files);
		}
		
		if($data['hasErrors']){
			$errors = $data['errors'];
			print_r($errors);
		}		
	}
	
	public function actionGetfileuploadname()
	{
		$session = Yii::$app->session;
		
		$output = array();
		
		$output['uri'] = Yii::$app->request->baseUrl . '/uploads/orders/'. Medias::findOne(base64_decode($session['__order_picture_id']))->filename;
		
		Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
		//return list
		return $output;		
	}
	
	function actionRemoveorderfile()
	{
		/*if(isset($_POST['file'])){
			$file = '../uploads/' . $_POST['file'];
			if(file_exists($file)){
				unlink($file);
			}
		}*/
	}
	
	function onFilesRemoveCallback($removed_files){
		foreach($removed_files as $key=>$value){
			$file = 'uploads/' . $value;
			if(file_exists($file)){
				unlink($file);
			}
		}
	
		return $removed_files;
	}
	
	/*
	public function actionListcountries()
	{
		$customers = Customer::find()->all();
		
		$output = array('query'=>'Unit');
		
		$suggestions = array();
		
		foreach($customers as $customer)
		{
			$suggestions[]=array('value' => $customer->companyname, 'data' => $customer->companyname);
		}
		//
		$output['suggestions'] = $suggestions;
		
		Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
		//return list
		return $output;
	}*/
	
	/*public function actionListcountries($query)
	{
		$searchstring = strtolower($query);
		
		$sql = "SELECT
			id, code, companyname
			FROM lv_customers
			WHERE LOWER(companyname) LIKE '%$searchstring%'
			OR LOWER(code) LIKE '%$searchstring%'
			ORDER BY companyname LIMIT 30";
				
		$models = Yii::$app->db->createCommand($sql)->queryAll();
		
		$output = array();
		
		$suggestions = array();
		
		foreach($models as $model)
		{
			$name = $model['companyname'];
			$suggestions['id']=$model['id'];
			$suggestions['name']=$name . ' (' . $model['code'] . ')';
			$output[] = $suggestions;
		}
		//$output['suggestions'] = $suggestions;
	
		Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
		//return list
		return $output;
	}*/
	
	public function actionListcountries($query)
	{
		$_search_string = '"%'.strtolower($query).'%"';
                $_sort_string = '"'.strtolower($query).'%"';
		$sql = 'SELECT * FROM `lv_customers` WHERE (lv_customers.firstname like '.$_search_string.' or lv_customers.lastname like '.$_search_string.' or lv_customers.companyname like '.$_search_string.' or lv_customers.code like '.$_search_string.') ORDER BY
                    CASE
                       WHEN firstname LIKE '.$_sort_string.' THEN 2
                       WHEN lastname LIKE '.$_sort_string.' THEN 4
                       WHEN companyname LIKE '.$_sort_string.' THEN 1
                       WHEN code LIKE '.$_sort_string.' THEN 3
                     ELSE 5
                    END LIMIT 30';
//		$sql = "SELECT
//			id, code, companyname
//			FROM lv_customers
//			WHERE LOWER(companyname) LIKE '%$searchstring%'
//			OR LOWER(code) LIKE '%$searchstring%'
//			ORDER BY companyname LIMIT 30";
				
		$models = Yii::$app->db->createCommand($sql)->queryAll();
		
		$output = array();
		
		$suggestions = array();
		
		foreach($models as $model)
		{
			$name = $model['companyname'];
			$suggestions['id']=$model['id'];
			$suggestions['name']=$name . ' (' . $model['code'] . ')';
			$output[] = $suggestions;
		}
		//$output['suggestions'] = $suggestions;
	
		Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
		//return list
		return $output;
	}	
	
	public function actionListcustomersinventory($query) 
	{
		$customers = Customer::find()
				->select('lv_customers.*')
				->join('INNER JOIN', 'lv_items', 'lv_items.customer =lv_customers.id')
				->where(['like', 'code', $query])
				->orwhere(['like', 'companyname', $query])
				->groupBy('customer')->all();
		
		$output = array();
		
		$suggestions = array();
		
		foreach($customers as $model)
		{
			$name = $model['companyname'];
			$suggestions['id'] = $model['id'];
			$suggestions['name'] = $name . ' (' . $model['code'] . ')';
			$output[] = $suggestions;
		}
		//$output['suggestions'] = $suggestions;
	
		Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
		//return list
		return $output;	
	}
	
	public function actionCheckmodelserial($modelid)
	{
		$model = Models::findOne($modelid);
		
		$output = array();
		
		$output['serialized'] = $model->serialized;
		
		Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
		//return list
		return $output;			
	}
	
	public function actionListaei($query)
	{
		$searchstring = strtolower($query);
		
		$sql = "SELECT
			id, aei
			FROM lv_models
			WHERE LOWER(aei) LIKE '%$searchstring%'
			LIMIT 30";
				
		$models = Yii::$app->db->createCommand($sql)->queryAll();
		
		$output = array();
		
		$suggestions = array();
		
		foreach($models as $model)
		{
			$suggestions['id']=$model['id'];
			$suggestions['name']=$model['aei'];
			$output[] = $suggestions;
		}
	
		Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
		//return list
		return $output;		
	}
	
	public function actionUpload()
	{
		$target_dir = "public/images/customers/";
		$file_name = basename($_FILES["file"]["name"]);
		$file_name_encoded = md5($file_name);
		$imageFileType = pathinfo($file_name,PATHINFO_EXTENSION);
		$target_file = $target_dir . $file_name_encoded . ".$imageFileType";
		$uploadOk = 1;
		//
		$session = Yii::$app->session;
		$session->set('__user_picture', $file_name_encoded . ".$imageFileType");
		/*$cookie = new Cookie([
				'name' => '__user_picture',
				'value' => $file_name_encoded,
				'expire' => time() + 86400,
				]);
		\Yii::$app->getResponse()->getCookies()->add($cookie);*/
		if (move_uploaded_file($_FILES["file"]["tmp_name"], $target_file)) {
			echo json_encode(array('status' => 'ok'));
		} else {
			echo json_encode(array('status' => 'error', 'message'=>"Sorry, there was an error uploading your file."));
		}
	}
	
	public function actionListshipmenttype()
	{
		$output = array('query'=>'Unit');
		
		$suggestions = array();
		
		$types = array('Primary', 'Conversion', 'Secondary');
		
		foreach($types as $key=>$type)
		{
			$suggestions[]=array('value' => $type, 'data' => $type);
		}
		//
		$output['suggestions'] = $suggestions;		
		Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
		//return list
		return $output;
	}
	
	/*public function actionListitem($query="sample response")
	{
		$models = Models::find()->limit(100)->all();
				
		$output = array();
		
		$suggestions = array();
				
		foreach($models as $model)
		{
			$name = Manufacturer::findOne($model->manufacturer)->name . ' ' . $model->descrip;
			$suggestions[$model->id]=$name;
		}
		$output['options'] = $suggestions;
		//
		Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
		//return list
		return $output;
	}*/

	/*
	 public function actionListitem($query='Toshiba')
	 {
	
	$searchstring = trim($query);
	
	$searchstring_2 = "";
	
	$model_sql = "lv_models.descrip LIKE '%$searchstring%' ";
	
	if (strpos($query, ' ') > 0) {
		
	$manufacturers = Manufacturer::find()->all();
		
	$manufacturer_id = null;
		
	foreach ($manufacturers as $manufacturer)
	{
	//echo strtolower($manufacturer->name);
	if($this->startsWith(strtolower($manufacturer->name), $searchstring))
	{
	$length = strlen($manufacturer->name);
	$searchstring_2 = substr($searchstring, $length+1);
	$manufacturer_id = $manufacturer->id;
	}
	}
		
	$searchstring_2 = explode(" ", $searchstring);
		
	$model_sql .= " OR (lv_models.manufacturer = '$manufacturer_id'";
				
			$count = count($searchstring_2);
				
			for($i=1;$i<$count;$i++){
			$model_sql .= " AND LOWER(lv_models.descrip) LIKE '%$searchstring_2[$i]%'";
			}
			$model_sql .= ")";
	}
	
	$sql = "SELECT
	lv_models.id,
	lv_models.descrip,
	lv_manufacturers.name,
	lv_partnumbers.partid,
	lv_models.aei,
	lv_models.frupartnum,
	lv_models.manpartnum
	FROM lv_models
	LEFT JOIN lv_manufacturers
	ON lv_models.manufacturer=lv_manufacturers.id
	LEFT JOIN lv_departements
	ON lv_models.department=lv_departements.id
	LEFT JOIN lv_partnumbers
	ON lv_partnumbers.model=lv_models.id
	WHERE $model_sql
	OR lv_models.aei LIKE '%$searchstring%'
	OR lv_models.frupartnum LIKE '%$searchstring%'
	OR lv_models.manpartnum LIKE '%$searchstring%'
	OR lv_manufacturers.name LIKE '%$searchstring%'
	OR lv_partnumbers.partid LIKE '%$searchstring%'
	OR lv_partnumbers.partdescription LIKE '%$searchstring%'
	LIMIT 10";
	
	$models = Yii::$app->db->createCommand($sql)->queryAll();
	
	$output = array();
	
	$suggestions = array();
	
	foreach($models as $model)
	{
	if(isset($model['partid']))
		$part_id = $model['partid'];
	else if(isset($model['manpartnum']))
		$part_id = $model['manpartnum'];
	else
		$part_id = $model['frupartnum'];
	$name = $model['name'] . ' ' . $model['descrip'];
	$suggestions['id']=$model['id'];
	$suggestions['name']=trim($name) . ' (' . $part_id . ')';
	$output[] = $suggestions;
	}
	//
	Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
	//return list
	return $output;
	}
	*/
	public function actionLoadtestingdetails($problem)
	{
		$_output = array();
		
		$itemtesting = Itemstesting::findOne($problem);
		
		$model = Models::findOne($itemtesting->partid);
		$manufacturer = Manufacturer::findOne($model->manufacturer);
	 				
		$_output['s1'] = $itemtesting->resolution;
						
		$_output['s2'] = $manufacturer->name . ' ' . $model->descrip;
		
		$_output['s3'] = $itemtesting->partid;
		
		Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
		return $_output; 		
	}
	
	public function actionLoadproblems()
	{
		$itemstesting = Itemstesting::find()->groupBy('problem')->all();
		
		$output = array();
	
		$suggestions = array();
	
		foreach($itemstesting as $itemtesting)
		{
			$suggestions['id']=$itemtesting->id;
			$suggestions['name']=$itemtesting->problem;
			$output[] = $suggestions;
		}	
		
		Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
		//return list
		return $output;		
	}
	
	public function actionLoadmodels($customer=null)
	{
		$cust= null;
		if(!empty($customer))
		{
			if(!is_numeric($customer)) 
				$cust= Customer::find()->where(['like', 'companyname', $customer])->one();
			else 
				$cust= Customer::findOne($customer);
		} 
		$sql = "SELECT 
						lv_models.id,
						CONCAT(lv_manufacturers.name, ' ',
						lv_models.descrip) as name,
						pn.partid, 
						lv_models.aei,
						lv_models.frupartnum,
						lv_models.manpartnum
						FROM lv_models
						LEFT JOIN lv_manufacturers
						ON lv_models.manufacturer=lv_manufacturers.id
						LEFT JOIN lv_departements
						ON lv_models.department=lv_departements.id";
		if($cust!==null)
			$sql .= " LEFT JOIN lv_partnumbers pn ON pn.model=lv_models.id AND pn.customer='". $cust->id ."'";
		else 
			$sql .= " LEFT JOIN lv_partnumbers pn
						ON pn.model=lv_models.id";
			
			$sql .= " WHERE descrip <> '' AND assembly = 0 GROUP BY lv_models.id";
		//echo Yii::$app->db->createCommand($sql)->rawSql;		
		$models = Yii::$app->db->createCommand($sql)->queryAll();		
		
		$output = array();
	
		$suggestions = array();
	
		foreach($models as $model)
		{
			$part_id = "";
			$model_aei = "";
			if(isset($model['partid'])) 
				$part_id = $model['partid'];
			$part_id = ($part_id!="") ? ' (' . $part_id . ')' : '';
			//$model_aei = ($model['aei']!="" && !empty($aei)) ? '(' . $model['aei'] . ') ' : '';
			//
			$name = $model['name'];
			if(!empty($name)) {
				$suggestions['id']=$model['id'];
				$suggestions['name']=trim($name) . $part_id;
				$output[] = $suggestions;
			}
		}
		//assemblies...
		/*$assemblies = Models::find()->where('descrip <> ""')->andWhere('manufacturer IS NULL')->andWhere('assembly=1')->all();
		//var_dump($assemblies);
		foreach($assemblies as $assembly)
		{
			$name = $assembly->descrip;
			$name = Html::encode($name);
			if(!empty($name)) {
				$suggestions['id']=$assembly->id;
				$suggestions['name']=trim($name);
				$output[] = $suggestions;	
			}
		}*/
		//store data in json file 
		$name = md5(uniqid().time());
		$_storepath = "public/autocomplete/json/$name.json";
		$session = Yii::$app->session;
		$old_file_generated = "." . $session['__autocomplete_json_generated_path'];
		if(!is_dir($old_file_generated) && file_exists($old_file_generated))
			unlink($old_file_generated);
		$session->set('__autocomplete_json_generated_path', "/" . $_storepath);
		$fp = fopen($_storepath, 'w');
		fwrite($fp, json_encode($output));
		fclose($fp);
		
		Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
		//return list
		return $output;
	}
	
	public function actionCheckmodelpreferedvendor()
	{
		if (Yii::$app->request->isAjax) {
			$_post = Yii::$app->request->get();
			
			$_retArray = array('success' => FALSE, 'html' => '');
			if (!isset($_post['itemid'])) {
				$_retArray = array('success' => FALSE, 'html' => 'Something is wrong! Plese try again!');
				echo json_encode($_retArray);
				exit();
			}
			$id = $_post['itemid'];	
		//
			$model = Models::findOne($id);
			$_retArray = array('success' => true, 'preferedvendor' => $model->prefered_vendor);
			echo json_encode($_retArray);
			exit();			
		}
	}
	
	public function actionGetorderjsonloaded()
	{
		$output = array();
		
		$session = Yii::$app->session;
		
		$output['name'] = $session['__autocomplete_json_generated_path'];
		
		Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
		//return list
		return $output;
	}
	
	public function actionListitem($query='')
	{
		
		$searchstring = trim(strtolower($query));
		
		$searchstring_2 = "";
		
		$model_sql = "LOWER(lv_models.descrip) LIKE '%$searchstring%' ";
		
		/*if (strpos($query, ' ') > 0) {
			
			$manufacturers = Manufacturer::find()->all();
			
			$manufacturer_id = null;
			
			foreach ($manufacturers as $manufacturer)
			{
				//echo strtolower($manufacturer->name);
				if($this->startsWith(strtolower($manufacturer->name), $searchstring))
				{
					$length = strlen($manufacturer->name);
					$searchstring_2 = substr($searchstring, $length+1);
					$manufacturer_id = $manufacturer->id;
				}
			}
			
			$searchstring_2 = explode(" ", $searchstring);
			
			$model_sql .= " OR (lv_models.manufacturer = '$manufacturer_id'";
			
			$count = count($searchstring_2);
			
			for($i=1;$i<$count;$i++){
				$model_sql .= " AND LOWER(lv_models.descrip) LIKE '%$searchstring_2[$i]%'";
			}
			$model_sql .= ")";
		}*/
		
		$sql = "SELECT 
						lv_models.id,
						CONCAT(lv_manufacturers.name, ' ',
						lv_models.descrip) as name,
						lv_partnumbers.partid,
						lv_models.aei,
						lv_models.frupartnum,
						lv_models.manpartnum
						FROM lv_models
						LEFT JOIN lv_manufacturers
						ON lv_models.manufacturer=lv_manufacturers.id
						LEFT JOIN lv_departements
						ON lv_models.department=lv_departements.id
						LEFT JOIN lv_partnumbers
						ON lv_partnumbers.model=lv_models.id
						WHERE ($model_sql
						OR lv_models.aei LIKE '%$searchstring%'						
						OR lv_models.frupartnum LIKE '%$searchstring%'	
						OR lv_models.manpartnum LIKE '%$searchstring%'	
						OR lv_manufacturers.name LIKE '%$searchstring%'					
						OR (LOWER(lv_models.descrip) LIKE '%$searchstring%' AND lv_models.manufacturer IS NULL)
						OR CONCAT(LOWER(lv_manufacturers.name), \" \", LOWER(lv_models.descrip)) LIKE '%$searchstring%')
						OR (lv_partnumbers.partid LIKE '%$searchstring%')
						OR (lv_partnumbers.partdescription LIKE '%$searchstring%')
						LIMIT 10";

		$models = Yii::$app->db->createCommand($sql)->queryAll();		
		
		$output = array();
	
		$suggestions = array();
	
		foreach($models as $model)
		{
			if(isset($model['partid']))
				$part_id = $model['partid'];
			else if(isset($model['manpartnum']))
				$part_id = $model['manpartnum'];
			else 
				$part_id = $model['frupartnum'];
			$part_id = ($part_id!="") ? ' (' . $part_id . ')' : '';
			$name = $model['name'];
			$suggestions['id']=$model['id'];
			$suggestions['name']=trim($name) . $part_id;
			$output[] = $suggestions;
		}
		//
		Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
		//return list
		return $output;
		
	}
	
	function startsWith($needle, $haystack) {
    	return preg_match('/^' . preg_quote($needle, '/') . '/', $haystack);
 	}
	
	public function actionGetlastordertype($customer=null)
	{
		/*if(!$customer)
			$ordertype=1;
		else
		{*/
			$customer=Customer::findOne($customer);
			if(!$customer)
				$ordertype=1;
			else 
				$find = Order::find()->where(['customer_id'=>$customer->id])->orderBy('id DESC')->one();
		//}
		$suggestions['ordertype']=$find->ordertype;
		$output[] = $suggestions;
		//
		Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
		//return list
		return $output;		
	}
	
	//public function actionGetpricing($customer='Wakefern', $ordertype='2', $idmodel='366')
	public function actionGetpricing($customer, $ordertype, $idmodel)
	{
		$_output = array();
		
		$suggestions = array();
		
		$cust= Customer::findOne($customer);
		
		$model = Models::findOne($idmodel);
		
		$price = 0;
		
		//checking default pricing on partnumber table
		$partnumber = Partnumber::find()->where(['customer'=>$cust->id, 'model'=>$model->id])->one();
		if($ordertype!=2)
		{
			if($partnumber !== null && !empty($partnumber->purchasepricing))
				$price = $partnumber->purchasepricing;
			else
				$price = $model->purchasepricing;
		} else {
			if($partnumber !== null && !empty($partnumber->repairpricing))
				$price = $partnumber->repairpricing;
			else
				$price = $model->repairpricing;			
		}
		
		if(empty($price) || $price==0) 
			$price = Itemsordered::findOne(['customer'=>$cust->id, 'ordertype'=>$ordertype, 'model'=>$idmodel])->price;

		$suggestions['price']=number_format(floatval($price), 2);
		$output[] = $suggestions;
		//
		Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
		//return list
		return $output;
	}
	
	public function actionGetpurchasedetails($idpurchase)
	{
		$_output = array();
		
		$suggestions = array();

		$purchase = Purchase::findOne($idpurchase);
				
		$output['number_generated'] = $purchase->number_generated;
		$output['user_id'] = $purchase->user_id;
		$output['vendor_id'] = $purchase->vendor_id;
		$output['shipping_deliverymethod'] = $purchase->shipping_deliverymethod;
		$shippingmethod = ShipmentMethod::findOne($purchase->shipping_deliverymethod);
		$output['shipping_company'] = ($shippingmethod!==null) ? ShippingCompany::findOne($shippingmethod->shipping_company_id)->name : null;
		$output['estimated_time'] = (!empty($purchase->estimated_time)) ? date('Y/m/d', strtotime($purchase->estimated_time)) : null;
		$output['trackingnumber'] = $purchase->trackingnumber;
		//
		Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
		//return list
		return $output;		
	} 
	
	public function actionGetuserdetails($id)
	{
		$_output = array();
		
		$suggestions = array();

		$user = Users::findOne($id);	
		
		$output['username'] = $user->username;
		$output['firstname'] = $user->firstname;
		$output['lastname'] = $user->lastname;
		
		Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
		//return list
		return $output;				
	}
	
	public function actionListcustomers()
	{
		$customers = Customer::find()->all();
		
		$_output = array();
	
		$suggestions = array();
	
		foreach($customers as $customer)
		{
			$suggestions[]=array('id' => $customer->id, 'name' => $customer->companyname);
		} 
		//
		$_output = $suggestions;
	 
		Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
		//return list
		return $_output; 		
	}
	
	public function actionGetinventorydetails($model)
	{
		$items = Item::findAll(['model'=>$model]);
		$customers = array();
		foreach($items as $item)
		{
			if($item->customer!=null)
				$customers[] = $item->customer;
		}
		//
		$customers = array_unique($customers);
		$customers = array_values($customers);
		$content = "";
		foreach($customers as $custid)
		{
			foreach(Item::$inventorystatus as $key=>$value)
			{
				$qty = Item::find()->where(['model'=>$model, 'customer'=>$custid, 'status' => "$key"])->count();
				$customername = Customer::findOne($custid)->companyname;
				if( $qty > 0)
					$content .= "Qty: $qty $value ($customername) <br/>";
			}
		}
		if(empty($content))
			$content = "No Informations found";
		return $content;
	}
	
	public function actionGetinventorypartnumbers($modelid)
	{
		$model = Models::findOne($modelid);
		$cust_partnums = Partnumber::find()->select('*')->where(['model'=>$model->id])->distinct()->all();
                if(Yii::$app->user->identity->usertype == User::REPRESENTATIVE){
                    $customers = ArrayHelper::getColumn(UserHasCustomer::find()->where(['userid'=>Yii::$app->user->id])->asArray()->all(), 'customerid');
                    $my_customers = "(".implode(",", array_map('intval', $customers)).")";
                    $cust_partnums = Partnumber::find()->select('*')
                            ->where(['model'=>$model->id])
                            ->andWhere('customer IN '.$my_customers)
                            ->distinct()->all();
                }
                $content = !empty($model->aei) ? "AEI #: " . $model->aei:"";
		$content .= !empty($model->manpartnum) ? "<br/>Manufacturers #: " . $model->manpartnum:"";
		$content .= !empty($model->frupartnum) ? "<br/>FRU #: " . $model->frupartnum:"";
		foreach($cust_partnums as $cust_partnum)
		{
			$content .= "<br/>" . Customer::findOne($cust_partnum->customer)->companyname . ' #: ' . $cust_partnum->partid . ' || ' . $cust_partnum->partdescription;
		}
		$content .= !empty($model->old_asset_number) ? "<br/>Old Asset Number:" . $model->old_asset_number : "";
		Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
		$_retArray = array('success' => true, 'html' => $content);
		return $_retArray;
		exit();
	}
	 
	public function actionGetitemsrequestedindexdetails($idorder, $itemid)
	{
		$_output = "";
		
		$order = Order::findOne($idorder);
		
		$model = Item::findOne($itemid);	
		
		$itemlog = Itemlog::find()->where(['itemid'=>$model->id])->one();
		
		/*$salesorderiprice = (new \yii\db\Query())->from('{{%itemsordered}}')
														->where(['model'=>$model->model])
														->andWhere(['ordernumber'=>$order->id])
														->sum('price');*/
		$salesorderiprice = Itemsordered::find()->where(['model'=>$model->model])->andWhere(['ordernumber'=>$order->id])->one()->price;
		
		$salesorderiqty = (new \yii\db\Query())->from('{{%itemsordered}}')
														->where(['model'=>$model->model])
														->andWhere(['ordernumber'=>$order->id])
														->sum('qty');
														
		$salesorderprice = 	($salesorderiqty!=0) ? $salesorderiprice : 0;		

		$lastitempurchaseorder = Purchase::find()->select('lv_purchases.*')->where(['model'=>$model->model])->innerJoin('lv_itemspurchased', '`lv_itemspurchased`.`ordernumber` = `lv_purchases`.`id`')->orderBy(['id' => SORT_DESC])->one();
		
		$lastpurchasedfrom = Vendor::findOne($lastitempurchaseorder->vendor_id);
		
		//$lastpurchaseprice = Itemspurchased::find()->where(['ordernumber'=>$order->id, 'model'=>$model->model])->orderBy(['id' => SORT_DESC])->one();
		
		$lastpurchaseprice = Itemspurchased::find()->where(['ordernumber'=>$lastitempurchaseorder->id, 'model'=>$model->model])->one()->price;
		
		$lastpurchasedon = $lastitempurchaseorder->created_at;
		
		//if((float) $salesorderprice !== floor($salesorderprice))
			setlocale(LC_MONETARY,"en_US");
			$salesorderprice = str_replace('USD ', '', money_format("%i", $salesorderprice));
		
		$_output .= "Requested For: " . Customer::findOne($order->customer_id)->companyname . " - " . $order->number_generated
				 . "</br>"
				 . "Requested By: " . Users::findOne($itemlog->userid)->firstname . ' ' . Users::findOne($itemlog->userid)->lastname[0] . '.'
				 . "</br>"
				 . "Requested Date: " .  date('m/d/Y', strtotime($model->created_at));
		$_output .= ($salesorderprice !== null) ? "</br>" . "Sales Order Price: $" . $salesorderprice : '';
		$_output .= ($lastpurchasedfrom !== null) ? "</br>" . "Last Purchased From: " . $lastpurchasedfrom->vendorname : '';
		$_output .= (!empty($lastpurchaseprice)) ? "</br>" . "Last purchase price: $" . $lastpurchaseprice : '';
		$_output .= (!empty($lastpurchasedon)) ? "</br>" . "Last Purchased On: " . date('m/d/Y', strtotime($lastpurchasedon)) : '';
		//
		Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
		$_retArray = array('success' => true, 'html' => $_output);
		return $_retArray;
		exit();
	}
	
	public function actionGetpurchaseindexdetails($idpurchase)
	{
		$purchase = Purchase::findOne($idpurchase);
		$items = Itemspurchased::find()->where(['ordernumber'=>$purchase->id])->all();
		$content = "";
		foreach($items as $item)
		{
			$_model = Models::findOne($item->model);
			$manufacturer = Manufacturer::findOne($_model->manufacturer);
			$count_model = Item::find()->where(['status'=>array_search('In Transit', Item::$status), 'purchaseordernumber'=>$purchase->id, 'model'=>$item->model])->count();
			$name = $manufacturer->name . ' ' . $_model->descrip;
			$departement = Department::findOne($_model->department)->name;
			$newline = "Qty: $count_model - $name ($departement)";
			if($name!=="" && strpos($content, $newline) === false)
				$content .= $newline . "<br/>";
		}
		//
		$_retArray = array('success' => true, 'html' => $content);
		
		Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
		//return view
		return $_retArray;
		exit();
	}
	
	public function actionGetorderindexdetails($idorder)
	{
		$model = Order::findOne($idorder);
		//TODO : for each order type, find status that order can have and for one order, list all status for each item groupby model
		/*if($model->ordertype==2){
			$number_items = Item::find()->where(['ordernumber'=>$model->id])
					->orWhere(['ordernumber'=>$model->id, 'status'=>array_search('Reserved', Item::$status)])
					->orWhere(['ordernumber'=>$model->id, 'status'=>array_search('In Transit', Item::$status)])
					->count();
		} else {*/
			$items = Itemsordered::find()->where(['ordernumber'=>$model->id])->all();
			//$qty = 0;
			/*foreach($items as $item)
			{
				$qty += $item->qty;
			}*/				
			//$number_items = $qty; 
		//}							
			$items = Itemsordered::find()->where(['ordernumber'=>$model->id])->all();
			$content = "";
			$qty = 0;
			foreach($items as $item)
			{
				$qty += $item->qty;
				$_model = Models::findOne($item->model);
				$manufacturer = Manufacturer::findOne($_model->manufacturer);
				$count_model = Itemsordered::find()->where(['ordernumber'=>$model->id, 'model'=>$item->model])->one()->qty;
				$name = $manufacturer->name . ' ' . $_model->descrip;
				$findstatus = Item::find()->where(['ordernumber'=>$model->id, 'model'=>$item->model])->groupBy('status')->all();
				$status = array();
				foreach($findstatus as $stat)
				{
					$status[] = Item::$status[$stat->status];
				}
				$newline = "($count_model) $name " . "<span style=\"color:#08c;\">(<b>" . implode(', ', $status) . "</b>)</span>";
				if($count_model > 0 && $name!=="" && strpos($content, $newline) === false)
					$content .= $newline . "<br/>";
			}	
			
			if(!empty($model->notes))
				$content .= '<small><i>Notes : ' . $model->notes . '</i></small>';
			
		/*$_output = $content;
		//
		echo $_output;*/
		$_retArray = array('success' => true, 'html' => $content);
		
		Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
		//return view
		return $_retArray;
		exit();
	}
	
	public function actionQgetorderindexdetails($idorder)
	{
		$model = QOrder::findOne($idorder);
		
		$items = QItemsordered::find()->where(['ordernumber'=>$model->id])->all();
		$qty = 0;
		foreach($items as $item)
		{
			$qty += $item->qty;
		}				
		$number_items = $qty;
		//
		$items = QItemsordered::find()->where(['ordernumber'=>$model->id])->all();
		$content = "";
		$qty = 0;
		foreach($items as $item)
		{
			$qty += $item->qty;
			$_model = Models::findOne($item->model);
			$manufacturer = Manufacturer::findOne($_model->manufacturer);
			$count_model = QItemsordered::find()->where(['ordernumber'=>$model->id, 'model'=>$item->model])->one()->qty;
			$name = $manufacturer->name . ' ' . $_model->descrip;
			$newline = "($count_model) $name ";
			if($name!=="" && strpos($content, $newline) === false)
				$content .= $newline . "<br/>";
		}		
		
		$_retArray = array('success' => true, 'html' => $content);
		
		Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
		//return view
		return $_retArray;
		exit();
	}
/*	
	public function actionGetorderindexstatus($idorder)
	{
		$model = Order::findOne($idorder);
		//
		$readytoshipstatus = array_search('Ready to ship', Item::$status);
		$pickedstatus = array_search('Picked', Item::$status);
		$inprogressstatus = array_search('In Progress', Item::$status);
		$intransitstatus = array_search('In Transit', Item::$status);
		$totalitems = Item::find()
			->where(['ordernumber'=>$model->id, 'status'=>$readytoshipstatus])
			->orWhere(['ordernumber'=>$model->id, 'status'=>$pickedstatus])
			->orWhere(['ordernumber'=>$model->id, 'status'=>$inprogressstatus])
			->orWhere(['ordernumber'=>$model->id, 'status'=>$intransitstatus])
			//->where(['and',['ordernumber'=>$model->id],['or',"status>$readytoshipstatus", "status=$pickedstatus", "status=$inprogressstatus", "status=$intransitstatus"]])
			->count();	
		//
		$completeitems = Item::find()
			->where(['ordernumber'=>$model->id])
			->andWhere(['status'=>array_search('Complete', Item::$status)])
			->count();
			
		$readytoshipitems = Item::find()
			->where(['ordernumber'=>$model->id])
			->andWhere(['status'=>array_search('Ready to ship', Item::$status)])
			->count();
			
		$inprogressitems = Item::find()
			->where(['ordernumber'=>$model->id])
			->andWhere(['status'=>$inprogressstatus])
			->count();
			
		$pickeditems = Item::find()
			->where(['ordernumber'=>$model->id])
			->andWhere(['status'=>$pickedstatus])
			->count();
		
		$higherstatusitems = Item::find()
			->where(['ordernumber'=>$model->id])
			->andWhere(['>','status', array_search('Ready to ship', Item::$status)])
			->count();
			
		$inprogresspickeditems = Item::find()
			->where(['and',['ordernumber'=>$model->id],['or',"status=$pickedstatus", "status=$inprogressstatus"]])
			->count();
			
		$intransititems = Item::find()
			->where(['ordernumber'=>$model->id])
			->andWhere(['status'=>array_search('In Transit', Item::$status)])
			->count();
	//
		//$completepercentage =  ($totalitems != 0) ? (($higherstatusitems * 100) / $totalitems) : 0;
		$completepercentage =  ($totalitems != 0) ? (($completeitems * 100) / $totalitems) : 0;
		$inprogresspickedpercentage = ($totalitems != 0) ? (($inprogresspickeditems * 100) / $totalitems) : 0;
		$intransitpercentage = ($totalitems != 0) ? (($intransititems * 100) / $totalitems) : 0;
		$readytoshippercentage =  ($totalitems != 0) ? (($readytoshipitems * 100) / $totalitems) : 0;
		$inprogresspercentage =  ($totalitems != 0) ? (($inprogressitems * 100) / $totalitems) : 0;
		$pickedpercentage =  ($totalitems != 0) ? (($pickeditems * 100) / $totalitems) : 0;
		//round percentages 
		if((float) $completepercentage !== floor($completepercentage))
			$completepercentage = round($completepercentage);
		if((float) $inprogresspickedpercentage !== floor($inprogresspickedpercentage))
			$inprogresspickedpercentage = round($inprogresspickedpercentage);
		if((float) $intransitpercentage !== floor($intransitpercentage))
			$intransitpercentage = round($intransitpercentage);
		if((float) $readytoshippercentage !== floor($readytoshippercentage))
			$readytoshippercentage = round($readytoshippercentage);
		if((float) $inprogresspercentage !== floor($inprogresspercentage))
			$inprogresspercentage = round($inprogresspercentage);
		if((float) $pickedpercentage !== floor($pickedpercentage))
			$pickedpercentage = round($pickedpercentage);
			$content = "";
			if($readytoshippercentage > 0)
				$content .= "$readytoshippercentage% Ready to Ship";
			if($inprogresspercentage > 0)
				$content .= " $inprogresspercentage% In Progress";
			if($pickedpercentage > 0)
				$content .= " $pickedpercentage% Picked";		
			if($intransitpercentage > 0)
				$content .= " $intransitpercentage% In Transit";	
			
		$_retArray = array('success' => true, 'html' => $content);
		
		Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
		//return view
		return $_retArray;
		exit();
	}*/
	
	public function actionGetorderindexstatus($idorder)
	{
		$model = Order::findOne($idorder);
		//
		$modelitems = Item::find()->where(['ordernumber'=>$model->id])->groupBy('model')->all();
		
		$totalitems = Item::find()->where(['ordernumber'=>$model->id])->count();	
		
		foreach($modelitems as $modelitem)
		{		
			$_model = Models::findOne($modelitem->model);
			
			$_manufacturer = Manufacturer::findOne($_model->manufacturer);
			
			$_pre_content = "Total $totalitems - <b>" . $_manufacturer->name . " " . $_model->descrip . "</b> ";
			
			$statuses = Item::find()->select('status')->where(['ordernumber'=>$model->id, 'model'=>$modelitem->model])->distinct()->orderBy('status')->all();
			
			if(count($statuses) > 1)
				$_pre_content .= "<br/>";
			
			$content = "";
			//
			foreach($statuses as $status)
			{
				//echo $status->status;
				
				$statusitems = Item::find()->where(['ordernumber'=>$model->id, 'status'=>$status->status])->count();
				
				$statuspercentage = ($totalitems != 0) ? (($statusitems * 100) / $totalitems) : 0;

				if((float) $statuspercentage !== floor($statuspercentage))
					$statuspercentage = round($statuspercentage);			
						
				if($statuspercentage > 0)
				{
					if(count($statuses) > 1)
						$content .= "<li style=\"margin-left:10px;list-style-type: square;\">Qty: $statusitems - ";
					$content .= "($statuspercentage% " . Item::$status[$status->status] . ")<br/>";
					if(count($statuses) > 1)
						$content .= "</li>";
				}
			}
			
			$content = $_pre_content . $content;
		}
		
			if(!empty($model->notes))
				$content .= '<small><i>Notes : ' . $model->notes . '</i></small>';
		//
		$_retArray = array('success' => true, 'html' => $content);
		
		Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
		//return view
		return $_retArray;
		exit();
	
	}
        public function actionGetinventorydata($id){
            $model = Models::findOne($id);
            $content = $sum = '';
            if($model['assembly'] == 1){
                if(Yii::$app->user->identity->usertype == User::REPRESENTATIVE){
                    $customers = ArrayHelper::getColumn(UserHasCustomer::find()->where(['userid'=>Yii::$app->user->id])->asArray()->all(), 'customerid');
                    if(!count($customers) ){
                        $customers = array(-1);
                    }
                    $number_items = ModelAssembly::find()
                        ->innerJoin('lv_partnumbers', '`lv_partnumbers`.`id` = `lv_model_assemblies`.`partid`')
                        ->where(['modelid'=>$model->id, 'customer' => $customers])
                        ->sum('quantity');
                } else {
                    $number_items = ModelAssembly::find()->where(['modelid'=>$model->id])->sum('quantity');
                }
                
		$items = ModelAssembly::find()->where(['modelid'=>$model->id])->all();
                $nbr_items_in_stock = Item::find()
                                ->innerJoin('lv_model_assemblies', '`lv_model_assemblies`.`partid` = `lv_items`.`model`')
                                ->where(['modelid'=>$model->id])
                                ->andwhere(['status'  => array_search('In Stock', Item::$status)]);
                if(Yii::$app->user->identity->usertype == User::REPRESENTATIVE){
                    $customers = ArrayHelper::getColumn(UserHasCustomer::find()->where(['userid'=>Yii::$app->user->id])->asArray()->all(), 'customerid');
                    if(!count($customers) ){
                        $customers = array(-1);
                    }
                    $nbr_items_in_stock->andWhere(['customer' => $customers]);
                }
//                                ->andwhere('status IN ('.array_search('In Stock', Item::$status).', '.array_search('Ready to ship', Item::$status).')')
                $nbr_items_in_stock->count();
                $sum = ($number_items!=0) ? $nbr_items_in_stock / $number_items : 0;
                foreach($items as $item)
                {
                    $customers = Item::find()->select('customer')->where(['model'=>$item->partid])->andwhere('status IN ('.array_search('In Stock', Item::$status))->groupBy('customer')->all();
//                    $customers = Item::find()->select('customer')->where(['model'=>$item->partid])->andwhere('status IN ('.array_search('In Stock', Item::$status).', '.array_search('Ready to ship', Item::$status).')')->groupBy('customer')->all();
                    if($nbr_items_in_stock>0) {
                        foreach($customers as $customer) {
                            foreach(Item::$inventorystatus as $key=>$value)
                            {
                                $qty = Item::find()->where(['model'=>$item->partid, 'customer'=>$customer->customer, 'status' => $key])->count();
                                $qty = ($nbr_items_in_stock!=0) ? ($qty * $sum / $nbr_items_in_stock) : 0;
                                //$sm += $qty;
                                if( $qty > 0) {
                                        $customername = Customer::findOne($customer->customer)->companyname;
                                        $_model = Models::findOne($item->partid);
                                        $_manufacturer = Manufacturer::findOne($_model->manufacturer);
                                        $newline = '(' . number_format($qty, 2) . ') ' . $_manufacturer->name . ' ' . $_model->descrip . ' '. $value . ' ('.$customername.')';
                                        if($name!=="" && strpos($content, $newline) === false)
                                                $content .= $newline . "<br/>";
                                }
                            }
                        }
                    }
                }   
            } else {
                if(Yii::$app->user->identity->usertype == User::REPRESENTATIVE){
                    $customers = ArrayHelper::getColumn(UserHasCustomer::find()->where(['userid'=>Yii::$app->user->id])->asArray()->all(), 'customerid');
                    if(!count($customers) ){
                        $customers = array(-1);
                    }
                    $my_customers = "(".implode(",", array_map('intval', $customers)).")";
                    $query = "SELECT DISTINCT(customer), companyname, status, COUNT(status) as nbr_per_status FROM lv_items
                                            INNER JOIN lv_customers ON lv_items.customer = lv_customers.id
                                        WHERE model= ".$model->id." AND status = ".array_search('In Stock', Item::$status)." AND customer IN ".$my_customers."
                                        GROUP BY status, customer
                                            ORDER BY companyname";
                } else {
                    $query = "SELECT DISTINCT(customer), companyname, status, COUNT(status) as nbr_per_status FROM lv_items
                                            INNER JOIN lv_customers ON lv_items.customer = lv_customers.id
                                        WHERE model= ".$model->id." AND status = ".array_search('In Stock', Item::$status)."
                                        GROUP BY status, customer
                                            ORDER BY companyname";
                }
//                $query = "SELECT DISTINCT(customer), companyname, status, COUNT(status) as nbr_per_status FROM lv_items
//                                            INNER JOIN lv_customers ON lv_items.customer = lv_customers.id
//                                        WHERE model= ".$model->id." AND status IN (".array_search('In Stock', Item::$status).", ".array_search('Ready to ship', Item::$status).", ".array_search('In Progress', Item::$status).")
//                                        GROUP BY status, customer
//                                            ORDER BY companyname
//                                    ";
                    $connection = Yii::$app->getDb();

                    $command = $connection->createCommand($query, [':model'=> $model->id]);

                    $rows = $command->queryAll();

                    foreach ($rows as $row)
                    {
                            $content .= "Qty: ".$row['nbr_per_status'] . ' '.Item::$inventorystatus[$row['status']] . " (".$row['companyname'].") <br/>";
                    }
            }
            if(empty($content))
                $content = "No Informations found";
            Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
            $_retArray = array('success' => true, 'html' => $content);
            //return view
            return $_retArray;
            exit();
        }
        
        
	/*
	public function actionGetorderindexdetails($type, $idorder)
	{
		//$_output = array();
		$_output = "";
		
		$suggestions = array();
		
		if($type==1){
			$order = Order::findOne($idorder);
			if($order->ordertype==2){
				$number_items = Item::find()->where(['ordernumber'=>$order->id])->count();
			} else {
				$items = Itemsordered::find()->where(['ordernumber'=>$order->id])->all();
				$qty = 0;
				foreach($items as $item)
				{
					$qty += $item->qty;
				}				
				$number_items = $qty;
			}
			//
			//$suggestions['id']=$idorder;
			//$suggestions['title']="Items ($number_items)";
			$_output = "Items ($number_items)";
		}
		else if($type==2)
		{
			$order = Order::findOne($idorder);
			if($order->ordertype==2){
				$items = Item::find()->where(['ordernumber'=>$order->id])->all();
				$content = "";
				foreach($items as $item)
				{
					$_model = Models::findOne($item->model);
					$manufacturer = Manufacturer::findOne($_model->manufacturer);
					$count_model = Item::find()->where(['ordernumber'=>$order->id, 'model'=>$item->model])->count();
					$name = $manufacturer->name . ' ' . $_model->descrip;
					$departement = Department::findOne($_model->department)->name;
					$newline = "Qty: $count_model - $name ($departement)";
					if($name!=="" && strpos($content, $newline) === false){
						$content .= $newline . '(' . $this->_items_status[$item->status] . ')' . "<br/>";
					}
				}
				$_output = $content;
			}else{
				$items = Itemsordered::find()->where(['ordernumber'=>$order->id])->all();
				$content = "";
				$qty = 0;
				foreach($items as $item)
				{
					$qty += $item->qty;
					$_model = Models::findOne($item->model);
					$manufacturer = Manufacturer::findOne($_model->manufacturer);
					$count_model = Itemsordered::find()->where(['ordernumber'=>$order->id, 'model'=>$item->model])->one()->qty;
					$name = $manufacturer->name . ' ' . $_model->descrip;
					$departement = Department::findOne($_model->department)->name;
					$newline = "Qty: $count_model - $name ($departement)";
					if($name!=="" && strpos($content, $newline) === false)
						$content .= $newline . "<br/>";
				}
				$_output = $content;
			}			
		}
		
		//$output[] = $suggestions;
		//
		//Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
		//return list
		echo $_output;
	}*/
	
	public function actionListconfigurationoptions($query='s')
	{
		$searchstring = strtolower($query);
		
		$sql = "SELECT
		id, name, parent_id
		FROM lv_model_options
		WHERE LOWER(name) LIKE '%$searchstring%' 
		AND checkable = 0
		ORDER BY name DESC LIMIT 30";
		
		$models = Yii::$app->db->createCommand($sql)->queryAll();
		
		$output = array();
		
		$suggestions = array();
		
		$parent_id = null;
		
		foreach($models as $model)
		{
			if(ModelOption::findOne($model['parent_id']) !== null) 
				$parent_id = '(' . ModelOption::findOne($model['parent_id'])->name . ')';
			$name = $model['name'] ;
			$suggestions['id']=$model['id'];
			$suggestions['name']=$name;
			$output[] = $suggestions;
		}
		//$output['suggestions'] = $suggestions;
		
		Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
		//return list
		return $output;		
	}
	
	public function actionSaveconfigurationoptions()
	{
		$data = array();
		
		$message = array();
		
		$errors = array();
		
		$data = Yii::$app->request->post();
		
		$parent_option = 0;
		if(isset($data['parent_option']) && $data['parent_option'] != "")
			$parent_option = $data['parent_option'];
				
		if ( ! empty($errors)) {
			// if there are items in our errors array, return those errors
			$message['success'] = false;
			$message['errors']  = $errors;
		} else {
			
			$option = new ModelOption;
			$option->idmodel = $data['idmodel'];
			$option->name = $data['option'];
			$option->optiontype = 2;
			$option->parent_id = $parent_option;
			$option->checkable = $data['checkable'];
			$option->save();
			// show a message of success and provide a true success variable
			$message['success'] = true;
			$message['message'] = "Element is succefully registered!";
		}
		//echo \yii\helpers\Json::encode($message);
		Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
		return $message;		
	}
	
	//verify customer.requireserials
	public function actionVerifycustomerserialstatus($customerid)
	{
		$message = array();
		
		$errors = array();
		
		$customer=Customer::findOne($customerid);
		if($customer->trackincomingserials==1)
			$message['success'] = true;
		else 
			$message['errors']  = true;
		
		Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
		return $message;
	}
	
	public function actionVerifycustomermodelserialstatus($customerid, $modelid)
	{
		$message = array();
		
		$errors = array();
		
		$customer=Customer::findOne($customerid);
		$model=Models::findOne($modelid);
		if($customer->trackincomingserials && $model->serialized)
			$message['success'] = true;
		else 
			$message['errors']  = true;
		
		Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
		return $message;
	}	
	
	public function actionVerifycustomerstorenumberstatus($customerid)
	{
		$message = array();
		
		$errors = array();
		
		$customer=Customer::findOne($customerid);
		
		if($customer->requirestorenumber)
			$message['success'] = true;
		else 
			$message['errors']  = true;
		
		Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
		return $message;
	}
	
	public function actionVerifycustomerpalletnumberstatus($customerid)
	{
		$message = array();
		
		$errors = array();
		
		$customer=Customer::findOne($customerid);
		
		if($customer->requirepalletcount)
			$message['success'] = true;
		else 
			$message['errors']  = true;
		
		Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
		return $message;
	}
	
	public function actionVerifycustomerboxnumberstatus($customerid)
	{
		$message = array();
		
		$errors = array();
		
		$customer=Customer::findOne($customerid);
		
		if($customer->requireboxcount)
			$message['success'] = true;
		else 
			$message['errors']  = true;
		
		Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
		return $message;
	}
	
	public function actionVerifycustomerboxnumber($customerid, $modelid)
	{
		$message = array();
		
		$errors = array();

		$boxnumber  = Item::find()->where(['model'=>$modelid, 'customer'=>$customerid])->orderBy('incomingboxnumber DESC')->one();
		
		if(empty($boxnumber))
			$message['errors']  = true;
		else {
			$message['success'] = true;
			$message['value'] = $boxnumber->incomingboxnumber;
		}
		
		Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
		return $message;
	}
	
	public function actionVerifycustomerpalletnumber($customerid, $modelid)
	{
		$message = array();
		
		$errors = array();

		$palletnumber  = Item::find()->where(['model'=>$modelid, 'customer'=>$customerid])->orderBy('incomingpalletnumber DESC')->one();
		
		//var_dump($palletnumber);
		
		if(empty($palletnumber))
			$message['errors']  = true;
		else {
			$message['success'] = true;
			$message['value'] = $palletnumber->incomingpalletnumber;
		}
		
		Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
		return $message;
	}
	
	public function actionGetdefaultlocations($customerid)
	{
		$result = array();
		
		$errors = array();
		
		$customer=Customer::findOne($customerid);

		$result['defaultreceivinglocation'] = $customer->defaultreceivinglocation;
		
		$result['defaultshippinglocation'] = $customer->defaultshippinglocation;
		
		$result['defaultbillinglocation'] = $customer->defaultbillinglocation;
		
		Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
		return $result;
	}
	
	public function actionLoadbillingoptions($id)
	{
		$result = array();
		
		$errors = array();
		
		$customer=Customer::findOne($id);

		$location = Location::findOne($customer->defaultbillinglocation);

		$result['address'] = $location->address;
		
		$result['address2'] = $location->address2;
		
		$result['country'] = $location->country;
		
		$result['city'] = $location->city;
		
		$result['state'] = $location->state;
		
		$result['zipcode'] = $location->zipcode;
		
		Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
		return $result;
	}
	
	public function actionVerifycustomercode($code)
	{
		$_check_customer = Customer::find()->where(['like', 'code', $code])->one();
		//
		if($_check_customer!==null)
			return 1;
		else 
			return 0;
	}

    public function actionIndex()
    {
        return "Ajax section...";
    }
}
