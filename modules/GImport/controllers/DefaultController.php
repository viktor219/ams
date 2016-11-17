<?php

namespace app\modules\GImport\controllers;

use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use app\modules\Orders\models\Order;
use app\models\User;
use app\components\AccessRule;
use yii\filters\AccessControl;
use yii\data\ActiveDataProvider;
use app\models\Gimpdivision;
use app\models\Gimplocation;
use app\models\Location;
use app\models\LocationClassment;
use app\models\LocationParent;
use app\models\Llocationcontact;
use app\models\Llocationdelete;
use app\models\Llocationdetail;
use app\models\LocationDetail;
use app\models\Luser;
use app\models\Users;
use app\models\LItemlocation;
use app\models\Models;
use app\models\LItemslocationI;
use app\models\Item;
use app\models\Itemlog;
use app\models\UserHasCustomer;
use app\models\LMitemlocation;
use app\models\MissingLocation;
use app\models\ISerialTagnumber;

class DefaultController extends Controller
{	
	public function behaviors()
	{
		return [
			'access' => [
				'class' => AccessControl::className(),
				// We will override the default rule config with the new AccessRule class
				'ruleConfig' => [
					'class' => AccessRule::className(),
				],
				'only' => ['index'],
				'rules' => [
					[
						'actions' => ['index'],
						'allow' => true,
						// Allow few users
						'roles' => [
							User::TYPE_ADMIN,
							User::TYPE_CUSTOMER_ADMIN,
							User::TYPE_SALES,
                            User::TYPE_SHIPPING,
                            User::TYPE_BILLING
						],
					]
				],
			]
		];
	}
		
    public function actionIndex()
    {
        return $this->render('index');
    }
    
    public function actionShippedlocation()
    {
    	$items = Item::find()->where(['customer'=>178, 'status'=>array_search('Shipped', Item::$status)])->all();
    	//
    	foreach($items as $item)
    	{
			$order = Order::findOne($item->ordernumber);
			//
			$item->location = $order->location_id;
			$item->save();
			//
			$itemlog = Itemlog::find()->where(['itemid'=>$item->id, 'status'=>array_search('Shipped', Item::$status)])->one();
			//
			if($itemlog === null)
			{
				$itemlog = new Itemlog;
				$itemlog->userid = Yii::$app->user->id;
				$itemlog->status = array_search('Shipped', Item::$status);
				$itemlog->itemid = $item->id;
				$itemlog->locationid = $item->location;
				$itemlog->save();				
			}
    	}
    }
    
    /**
     * CLI command importing
     */
    
    public function actionUpdatedivision()
    {
    	$divisions = Gimpdivision::find()->all();
    	
    	foreach ($divisions as $division)
    	{
    		$_find_division = LocationParent::find()->where(['parent_code'=>$division->did])->one();
    		
    		if($_find_division===null)
    			$_find_division = new LocationParent;
    		
    		$_find_division->parent_code = $division->did;
    		
    		$_find_division->parent_name = $division->dname;
    		
    		$_find_division->save();
    	}
    	
    	echo 'Divisions updated sucessfully!';
    	exit();
    }
    
    public function actionRecoverlocation()
    {
    	$models_imported = Gimplocation::find()->all();
    	
    	foreach($models_imported as $model_imported)
    	{
    		$parent_id = 1;
    
    		$storenum = $model_imported->storenum;
    
    		if(strlen($storenum) > 4) {
    			if (strpos($storenum, 'AWGNH') !== false)
    			{
    				$storenum = str_replace('AWGNH', '', $storenum);
    				$parent_id = 2;
    			}
    			if (strpos($storenum, 'AWGLOST') !== false)
    			{
    				$storenum = str_replace('AWGLOST', '', $storenum);
    				$parent_id = 3;
    			}
    			if (strpos($storenum, 'CORP') !== false)
    			{
    				$storenum = str_replace('CORP', '', $storenum);
    				$parent_id = 4;
    			}
    			if (strpos($storenum, 'GC') !== false)
    			{
    				$storenum = str_replace('GC', '', $storenum);
    				$parent_id = 5;
    			}
    			if (strpos($storenum, 'KC') !== false)
    			{
    				$storenum = str_replace('KC', '', $storenum);
    				$parent_id = 6;
    			}
    			if (strpos($storenum, 'SP') !== false)
    			{
    				$storenum = str_replace('SP', '', $storenum);
    				$parent_id = 7;
    			}
    			if (strpos($storenum, 'OK') !== false)
    			{
    				$storenum = str_replace('OK', '', $storenum);
    				$parent_id = 8;
    			}
    			if (strpos($storenum, 'GO') !== false)
    			{
    				$storenum = str_replace('GO', '', $storenum);
    				$parent_id = 9;
    			}
    			if (strpos($storenum, 'SO') !== false)
    			{
    				$storenum = str_replace('SO', '', $storenum);
    				$parent_id = 10;
    			}
    			if (strpos($storenum, 'FW') !== false)
    			{
    				$storenum = str_replace('FW', '', $storenum);
    				$parent_id = 11;
    			}
    			if (strpos($storenum, 'VM') !== false)
    			{
    				$storenum = str_replace('VM', '', $storenum);
    				$parent_id = 12;
    			}
    		}
    
    		$deleted = 0;
    
    		if($storenum[0] == "0")
    			$storenum = substr($storenum, 1);
    		
    		
    	}
    }
    
    public function actionUpdatelocation()
    {
    	$models_imported = Gimplocation::find()->all();
    	
    	//$i=0;
    	    	
    	foreach($models_imported as $model_imported)
    	{
    		$parent_id = 1;
    		
    		$storenum = $model_imported->storenum;
    		
    		if(strlen($storenum) > 4) {
	    		if (strpos($storenum, 'AWGNH') !== false)
	    		{
	    			$storenum = str_replace('AWGNH', '', $storenum);
	    			$parent_id = 2;
	    		}
	    		if (strpos($storenum, 'AWGLOST') !== false)
	    		{
	    			$storenum = str_replace('AWGLOST', '', $storenum);
	    			$parent_id = 3;
	    		}
	    		if (strpos($storenum, 'CORP') !== false)
	    		{
	    			$storenum = str_replace('CORP', '', $storenum);
	    			$parent_id = 4;
	    		}
	    		if (strpos($storenum, 'GC') !== false)
	    		{
	    			$storenum = str_replace('GC', '', $storenum);
	    			$parent_id = 5;
	    		}
	    		if (strpos($storenum, 'KC') !== false)
	    		{
	    			$storenum = str_replace('KC', '', $storenum);
	    			$parent_id = 6;
	    		}
	    		if (strpos($storenum, 'SP') !== false)
	    		{
	    			$storenum = str_replace('SP', '', $storenum);
	    			$parent_id = 7;
	    		}
	    		if (strpos($storenum, 'OK') !== false)
	    		{
	    			$storenum = str_replace('OK', '', $storenum);
	    			$parent_id = 8;
	    		}
	    		if (strpos($storenum, 'GO') !== false)
	    		{
	    			$storenum = str_replace('GO', '', $storenum);
	    			$parent_id = 9;
	    		}
	    		if (strpos($storenum, 'SO') !== false)
	    		{
	    			$storenum = str_replace('SO', '', $storenum);
	    			$parent_id = 10;
	    		}
	    		if (strpos($storenum, 'FW') !== false)
	    		{
	    			$storenum = str_replace('FW', '', $storenum);
	    			$parent_id = 11;
	    		}
	    		if (strpos($storenum, 'VM') !== false)
	    		{
	    			$storenum = str_replace('VM', '', $storenum);
	    			$parent_id = 12;
	    		}
    		}
    		
    		$deleted = 0;
    		
    		if($storenum[0] == "0")
    			$storenum = substr($storenum, 1);
    		
    		$storename = $model_imported->storename;
    		
    		$storenames = explode('-', $storename);
    		
    		$case = ucwords(strtolower(trim($storenames[0])));
    		
    		if(!empty($storenames[1]))
    			$storename = "$case - " . trim($storenames[1]);
    		else 
    			$storename = $case;
    		
    		if(!empty($model_imported->notes))
    		{
    			if (strpos(strtolower($model_imported->notes), 'delete') !== false)
    				$deleted = 1;
    		}
    		
    		//$location = Location::find()->where(['storenum'=>$storenum, 'customer_id'=>178])->one();

			//if(empty($location))
			if($deleted==0)
			{
				$location = new Location;
			
				$location->deleted = $deleted;
				
	    		$location->customer_id = 178;
	    		
	    		$location->storenum = $storenum;
	    		
	    		$location->storename = $storename;
	    		
	    		$location->address = $model_imported->address;
	    		
	    		$location->city = $model_imported->city;
	    		
	    		$location->state = $model_imported->state;
	    		
	    		$location->zipcode = $model_imported->zipcode;
	    		
	    		$location->notes = $model_imported->notes;
	    		
	    		$location->save();   
	
	    		$parent_location = LocationClassment::find()->where(['location_id'=>$location->id])->one();
	    		
	    		if(!empty($parent_location))
	    			$parent_location->delete();
	    		
	    		$lmodel = new LocationClassment;
	    		$lmodel->parent_id = $parent_id;
	    		$lmodel->location_id = $location->id;
	    		$lmodel->save(); 
			}
    		//$i++;
    	}    	
    	
    	echo "Locations updated successfully!";
    	//exit();
    }
    
    public function actionUpdatelocationcontact()//email, phone
    {
    	$i_locations = Llocationcontact::find()->all();
    	
    	$i=0;
    	
    	foreach($i_locations as $i_location)
    	{
    		$division = str_replace('VM', 'VMC', $i_location->division_id);
    		
    		$division = LocationParent::find()->where(['parent_code'=>$division])->one()->id;
    		
    		$_find_location = Location::find()->innerJoin('lv_locations_classments', '`lv_locations_classments`.`location_id` = `lv_locations`.`id`')
    										->where(['customer_id'=>178, 'zipcode'=>$i_location->zipcode, 'parent_id'=>$division])
    										->one();
    		 
    		//$location_parent = LocationParent::find()->where(['parent_code'=>$i_location->division_id])->one();
    		if(!empty($_find_location))
    		{
    			//$i++;
	    		//$_find_location = new Location;
	    		//$_find_location->customer_id = 178;
	    		//$_find_location->storenum = $i_location->storenum;
	    		//$_find_location->storename = $i_location->storename;
	    		//$_find_location->address = $i_location->address;
	    		//$_find_location->city = $i_location->city;
	    		//$_find_location->state = $i_location->state;
	    		//$_find_location->zipcode = $i_location->zipcode;
	    		$_find_location->phone = $i_location->phone;
	    		$_find_location->email = ucfirst(strtolower($i_location->email));
	    		$_find_location->save();
    		}
    		/*$locationclassment = new LocationClassment;
    		$locationclassment->parent_id = $location_parent->id;
    		$locationclassment->location_id = $_find_location->id;
    		$locationclassment->save();*/
    	}

    	echo "Location contact updated succesfully!";
    }
    
    public function actionUpdatedeletedlocations()
    {
    	$i_locations = Llocationdelete::find()->all();
    	
    	foreach($i_locations as $i_location)
    	{
    		$division = str_replace('VM', 'VMC', $i_location->division_id);
    		
    		$division = LocationParent::find()->where(['parent_code'=>$division])->one()->id;  		
    		
    		$_find_location = Location::find()->innerJoin('lv_locations_classments', '`lv_locations_classments`.`location_id` = `lv_locations`.`id`')
										    		->where(['customer_id'=>178, 'zipcode'=>$i_location->zipcode, 'parent_id'=>$division])
										    		->one();   
    		if(!empty($_find_location))
    		{
    			$_find_location->deleted = 1;
    			$_find_location->save();
    		}
    	}
    	
    	echo "Locations have moved properly like deleted!";
    }
    
   public function actionUpdateconnectiondata() //update dhcp for all locations which don't have connection type.
    {
    	$i_locations = Llocationdetail::find()->all();
    	 
    	foreach($i_locations as $i_location)
    	{
    		$location = Location::find()->where(['storenum'=>$i_location->storenum])->one();
    		
    		//if(empty($location))
    			//echo $location->storenum;
    		
    		if(!empty($location))
    		{
    			$location->connection_type = $i_location->connection_type;
    			$location->save();
    			//
    			$locationdetail = LocationDetail::find()->where(['locationid'=>$location->id])->one();
    			
    			if(empty($locationdetail))
    				$locationdetail = new LocationDetail;
    			
    			$locationdetail->locationid = $location->id;
    			$locationdetail->ipaddress = $i_location->ipaddress;
    			$locationdetail->subnet_mask = $i_location->subnet_mask;
    			$locationdetail->gateway = $i_location->gateway;
    			$locationdetail->primary_dns = $i_location->primary_dns;
    			$locationdetail->secondary_dns = $i_location->secondary_dns;
    			$locationdetail->wins_server = $i_location->wins_server;
    			$locationdetail->save();
    		}
    	}   

    	echo "Location details saved!";
    }
    
    public function actionUpdateusers()
    {
    	$_users = array(
    			9 => 'Representative',
    			1 => 'Administrator'
    	);

    	$users = Luser::find()->all();
    	
    	foreach($users as $i_user)
    	{
    		$division = str_replace('VM', 'VMC', $i_user->division);
    		
    		$division = LocationParent::find()->where(['parent_code'=>$division])->one()->id;    	

    		//echo $i_user->access.'</br>';
    		
    		//$user = Users::find()->where(['username'=>$i_user->username])->one();
    		
    		//if(empty($user))
				$user = new Users;
    		
    		$user->firstname = $i_user->firstname;
    		
    		$user->lastname = $i_user->lastname;
    		
    		$user->username = $i_user->username;
    		
    		$user->email = $i_user->email;
    		
    		$user->hash_password = md5('awgpassword');
    		
    		$user->division_id = $division;
    		
    		$user->usertype = array_search($i_user->access, $_users);
    		
    		$user->save();
    		
    		$user_has_customer = new UserHasCustomer;
    		
    		$user_has_customer->userid = $user->id;
    		
    		$user_has_customer->customerid = 178;
    		
    		$user_has_customer->save();
    		
    	}
    }
    
    public function actionConvertitemsmodel()
    {
    	ini_set('max_execution_time', 120);
    	ini_set('memory_limit', '512M');
    	
    	$items = LItemlocation::find()->where(['not', ['model'=>["5242", "5239", "5236", "5240", "5241"]]])->all();
    	
    	foreach($items as $i_item)
    	{
    		$_model = Models::find()->where(['manpartnum'=>$i_item->model])->one();
    		
    		//echo $_model->id;exit();
    		
    		//$item = Item::findOne($i_item->id);
    		
    		$i_item->model = $_model->id;
    		
    		$i_item->save();
    	}
    }
    
    public function actionAddmissinglocation()
    {
    	$items = MissingLocation::find()->all();
    	
    	foreach($items as $i_item)
    	{
    		$__find_location = Location::find()->innerJoin('lv_locations_classments', '`lv_locations_classments`.`location_id` = `lv_locations`.`id`')->where(['customer_id'=>178, 'storenum'=>$i_item->storenum, 'parent_id'=>$i_item->division_id])->one();
    		
    		if(empty($__find_location) && ( strpos($storenum, 'Delete') === false || strpos($storenum, 'CLOSED') === false))
    		{
    			//var_dump($i_item->storename);
    			
    			$storename = $i_item->storename;
    			
    			$storenames = explode('-', $storename);
    			
    			$case = ucwords(strtolower(trim($storenames[0])));
    			
    			if(!empty($storenames[1]))
    				$storename = "$case - " . trim($storenames[1]);
    			else
    				$storename = $case;
    			
    			$storenum = $i_item->storenum;
    			
    			$location = new Location;
    				
    			$location->deleted = 0;
    			
    			$location->customer_id = 178;
    				
    			$location->storenum = $storenum;
    				
    			$location->storename = $storename;
    				
    			$location->address = $i_item->address;
    				
    			$location->city = $i_item->city;
    				
    			$location->state = $i_item->state;
    				
    			$location->zipcode = $i_item->zipcode;
    				
    			$location->phone = $i_item->phone;
    				
    			$location->save();
    			
    			$lmodel = new LocationClassment;
    			$lmodel->parent_id = $i_item->division_id;
    			$lmodel->location_id = $location->id;
    			$lmodel->save();  			
    		}
    	}    	
    }
    
    public function actionConvertlocationname()
    {
    	$items = LItemlocation::find()->all();

    	foreach($items as $i_item)
    	{
    		$storenum = str_replace('AWG', '', $i_item->site_id);
    
    		$storenum = trim($storenum);
    		
    		$i_item->site_id = $storenum;
    		 
    		$i_item->save();    		
    	}
    }
    
    public function actionConverlocationfirst()
    {
    	ini_set('max_execution_time', 120);
    	ini_set('memory_limit', '512M');
    	
    	//$items = \yii\helpers\ArrayHelper::getColumn(LItemlocation::find()->where(['like', 'site_id', 'SP'])->all(), 'id');
    	//LItemlocation::deleteAll(['not', ['id'=>$items]]);
    	$items = LItemlocation::find()->all();
    	//var_dump($items);
      	foreach($items as $i_item)
    	{
    		$storenum = str_replace('AWG', '', $i_item->site_id);
    		
    		$storenum = trim($storenum);
    		
    		$parent_id = 1;
    		
    		if (strpos($storenum, 'LOST') !== false)
    		{
    			$parent_id = 3;
    		}
    		else if (strpos($storenum, 'CORP') !== false || strpos($storenum, 'MISC') !== false)
    		{
    			$parent_id = 4;
    		}
    		else if (strpos($storenum, 'GC') !== false)
    		{
    			$parent_id = 5;
    		}
    		else if (strpos($storenum, 'KC') !== false)
    		{
    			$parent_id = 6;
    		}
    		else if (strpos($storenum, 'SP') !== false)
    		{
    			$parent_id = 7;
    		}
    		else if (strpos($storenum, 'OK') !== false)
    		{
    			$parent_id = 8;
    		}
    		else if (strpos($storenum, 'GO') !== false)
    		{
    			$parent_id = 9;
    		}
    		else if (strpos($storenum, 'SO') !== false)
    		{
    			$parent_id = 10;
    		}
    		else if (strpos($storenum, 'FW') !== false)
    		{
    			$parent_id = 11;
    		}
    		else if (strpos($storenum, 'VM') !== false)
    		{
    			$parent_id = 12;
    		}
    		
    		if(strlen($storenum) > 4)
    		{
	    		if (strpos($storenum, 'LOST') !== false)
	    		{
	    			$storenum = str_replace('LOST', '', $storenum);
	    		}
	    		else if (strpos($storenum, 'CORP') !== false)
	    		{
	    			$storenum = str_replace('CORP', '', $storenum);
	    		}
	    		else if (strpos($storenum, 'GC') !== false)
	    		{
	    			$storenum = str_replace('GC', '', $storenum);
	    		}
	    		else if (strpos($storenum, 'KC') !== false)
	    		{
	    			$storenum = str_replace('KC', '', $storenum);
	    		}
	    		else if (strpos($storenum, 'SP') !== false)
	    		{
	    			$storenum = str_replace('SP', '', $storenum);
	    		}
	    		else if (strpos($storenum, 'OK') !== false)
	    		{
	    			$storenum = str_replace('OK', '', $storenum);
	    		}
	    		else if (strpos($storenum, 'GO') !== false)
	    		{
	    			$storenum = str_replace('GO', '', $storenum);
	    		}
	    		else if (strpos($storenum, 'SO') !== false)
	    		{
	    			$storenum = str_replace('SO', '', $storenum);
	    		}
	    		else if (strpos($storenum, 'FW') !== false)
	    		{
	    			$storenum = str_replace('FW', '', $storenum);
	    		}
	    		else if (strpos($storenum, 'VM') !== false)
	    		{
	    			$storenum = str_replace('VM', '', $storenum);
	    		}
    		}
    		if(strlen($storenum)==5 && $storenum[0] == "0")
    			$storenum = substr($storenum, 1);   
    	$site_id = (int) $i_item->site_id;
    	if($site_id==0)
			$i_item->delete();
    		/*$__find_location = Location::find()->innerJoin('lv_locations_classments', '`lv_locations_classments`.`location_id` = `lv_locations`.`id`')->where(['customer_id'=>178, 'storenum'=>$storenum, 'parent_id'=>$parent_id])->one();
    		
    		if(!empty($__find_location))
    		{
    			$i_item->site_id = $__find_location->id;
    			
    			$i_item->save();
    		} /*else {
    			$model = Gimplocation::find()->where(['storenum'=>$i_item->site_id])->one();
    			
    			if(!empty($model))
    			{
    				$storename = $model->storename;
    				
    				$storenames = explode('-', $storename);
    				
    				$case = ucwords(strtolower(trim($storenames[0])));
    				
    				if(!empty($storenames[1]))
    					$storename = "$case - " . trim($storenames[1]);
    				else
    					$storename = $case;
    				
    				$location = new Location;
    					
    				$location->deleted = 0;
    				
    				$location->customer_id = 178;
    				 
    				$location->storenum = $storenum;
    				 
    				$location->storename = $storename;
    				 
    				$location->address = $model->address;
    				 
    				$location->city = $model->city;
    				 
    				$location->state = $model->state;
    				 
    				$location->zipcode = $model->zipcode;
    				 
    				$location->notes = $model->notes;
    				 
    				$location->save();
    				
    				//$parent_location = LocationClassment::find()->where(['location_id'=>$location->id])->one();
    				 
    				//if(!empty($parent_location))
    				//	$parent_location->delete();
    				 
    				$lmodel = new LocationClassment;
    				$lmodel->parent_id = $parent_id;
    				$lmodel->location_id = $location->id;
    				$lmodel->save();    
    				//
    				$i_item->site_id = $location->id;
    				 
    				$i_item->save();
    			}
    		}*/
    	}    	
    }
    
    public function actionConvertlocation()
    {
    	ini_set('max_execution_time', 120);
    	ini_set('memory_limit', '512M');
    	 
    	//$items = \yii\helpers\ArrayHelper::getColumn(LItemlocation::find()->where(['like', 'site_id', 'SP'])->all(), 'id');
    	//LItemlocation::deleteAll(['not', ['id'=>$items]]);
    	$items = LItemlocation::find()->all();
    	//var_dump($items);
    	foreach($items as $i_item)
    	{
    		$storenum = str_replace('AWG', '', $i_item->site_id);
    		
    		$storenum = trim($storenum);
    		 
    		if (strpos($storenum, 'SP') !== false)
    		{
    			$storenum = str_replace('SP', '', $storenum);
    			$parent_id = 7;
    		}
    		
    		if(strlen($storenum)==5 && $storenum[0] == "0")
    			$storenum = substr($storenum, 1);   

    		$__find_location = Location::find()->innerJoin('lv_locations_classments', '`lv_locations_classments`.`location_id` = `lv_locations`.`id`')->where(['customer_id'=>178, 'storenum'=>$storenum, 'parent_id'=>$parent_id])->one();
    		
    		if(!empty($__find_location))
    		{
    			$i_item->site_id = $__find_location->id;
    			
    			$i_item->save();
    		}
    	}   	
    }
    
    public function actionUpdateserialtagnumber()
    {
    	$items = ISerialTagnumber::find()->all();
    	//
    	foreach ($items as $i_item)
    	{
    		$finditem = Item::find()->where(['serial'=>$i_item->serial])->andWhere(['customer'=>178])->one();
    		
    		if(!empty($finditem))
    		{
    			$finditem->tagnum = $i_item->tagnum;
    			$finditem->save();
    			//
    			$i_item->delete();
    		}
    	}
    }
    
    public function actionAdditemslocation()
    {
    	ini_set('max_execution_time', 120);
    	ini_set('memory_limit', '512M');
    	
    	$items = LItemlocation::find()->all();
    	 
    	foreach($items as $i_item)
    	{
    		/*$storenum = str_replace('AWG', '', $i_item->site_id);
    		
    		$storenum = trim($storenum);
    		 
    		if (strpos($storenum, 'NOHOME') !== false)
    		{
    			//$storenum = str_replace('NOHOME', '', $storenum);
    			$parent_id = 2;
    		}
    		if (strpos($storenum, 'LOST') !== false)
    		{
    			//$storenum = str_replace('LOST', '', $storenum);
    			$parent_id = 3;
    		}
    		if (strpos($storenum, 'CORP') !== false)
    		{
    			$storenum = str_replace('CORP', '', $storenum);
    			$parent_id = 4;
    		}
    		if (strpos($storenum, 'GC') !== false)
    		{
    			$storenum = str_replace('GC', '', $storenum);
    			$parent_id = 5;
    		}
    		if (strpos($storenum, 'KC') !== false)
    		{
    			$storenum = str_replace('KC', '', $storenum);
    			$parent_id = 6;
    		}
    		if (strpos($storenum, 'SP') !== false)
    		{
    			$storenum = str_replace('SP', '', $storenum);
    			$parent_id = 7;
    		}
    		if (strpos($storenum, 'OK') !== false)
    		{
    			$storenum = str_replace('OK', '', $storenum);
    			$parent_id = 8;
    		}
    		if (strpos($storenum, 'GO') !== false)
    		{
    			$storenum = str_replace('GO', '', $storenum);
    			$parent_id = 9;
    		}
    		if (strpos($storenum, 'SO') !== false)
    		{
    			$storenum = str_replace('SO', '', $storenum);
    			$parent_id = 10;
    		}
    		if (strpos($storenum, 'FW') !== false)
    		{
    			$storenum = str_replace('FW', '', $storenum);
    			$parent_id = 11;
    		}
    		if (strpos($storenum, 'VM') !== false)
    		{
    			$storenum = str_replace('VM', '', $storenum);
    			$parent_id = 12;
    		}
    		if($storenum[0] == "0")
    			$storenum = substr($storenum, 1);
    		 
    		//$__find_location = Location::find()->where(['customer_id'=>178, 'storenum'=>$storenum])->one();
    		   		
    		//if($storenum=='DIV')
    			$__find_location = Location::find()->innerJoin('lv_locations_classments', '`lv_locations_classments`.`location_id` = `lv_locations`.`id`')->where(['customer_id'=>178, 'storenum'=>$storenum, 'parent_id'=>$parent_id])->one();
    		
    		//if($parent_id!=2)
    			//echo $storenum;
    		
    		/*if(empty($__find_location))
    		{
    			$__find_location = new Location;
    			$__find_location->customer_id = 178;
    			$__find_location->storenum = $storenum;
    			$__find_location->save();
 				//
    			$locationclassment = new LocationClassment;
    			$locationclassment->parent_id = $parent_id;
    			$locationclassment->location_id = $__find_location->id;
    			$locationclassment->save();   			
    		}*/
    		$_find_item = Item::find()->where(['customer'=>178, 'serial'=>$i_item->serialnumber, 'tagnum'=>$i_item->tagnumber])->one();
    		if($_find_item===null)
    		{
	    		$_model = (int) $i_item->model;
	    		if($_model==5237)
	    			$_model = 5236;
	    		else if($_model==5241)
	    			$_model = 5240;
	    		
	    		//echo $_model;
	    		$model = new Item;
	    		$model->customer = 178;
	    		$model->serial = $i_item->serialnumber;
	    		$model->location = $i_item->site_id;
	    		$model->model = $_model; 
	    		$model->tagnum = $i_item->tagnumber;
	    		$model->status = 16;
				$model->imported = 1;
	    		//$model->shipped = date('Y-m-d H:i:s', strtotime($item->shipped));
	    		$model->save();
	    		//track item
	    		$itemlog = new Itemlog;
	    		$itemlog->userid = 2;
	    		$itemlog->status = 16;
	    		$itemlog->itemid = $model->id;
	    		$itemlog->locationid = $i_item->site_id;
	    		$itemlog->save();  
    		} 		
    	}    	
    }
    
    public function actionMaddinglocation()
    {
    	$items = LMitemlocation::find()->all();
    	
    	foreach($items as $i_item)
    	{
    		$storenum = str_replace('Store.', '', $i_item->storenum);
    		
    		$storenum = trim($storenum);
    		
    		$__find_location = Location::find()->where(['customer_id'=>178, 'storenum'=>$storenum])->one();
    	}    	
    }    
    /*ini_set('max_execution_time', 120);
    ini_set('memory_limit', '512M');
    
    $items = UpdateItemLocation::find()->all();
    
    foreach($items as $item)
    {
    	$__item = Item::find()->where(['customer'=>178, 'serial'=>$item->serial])->one();
    		
    	if($__item !== null)
    	{
    		$storenum = $item->storenum;
    		if(strlen($storenum)==2)
    			$storenum = "00$storenum";
    		else if(strlen($storenum)==3)
    			$storenum = "0$storenum";
    
    		$location = Location::find()->where(['customer_id'=>178, 'storenum'=>$storenum])->one();
    			
    		$__item->tagnum = $item->tagnum;
    
    		if($location!==null)
    			$__item->location = $location->id;
    
    		$__item->save();
    	}
    }
    
    $sql = "SELECT GROUP_CONCAT(id) as ids, serial, COUNT( * ) c
     FROM `lv_items`
    WHERE serial <> ''
    AND serial IS NOT NULL
    AND `customer` =178
    GROUP BY serial
    HAVING c >1";
    
    $_items = Yii::$app->db->createCommand($sql)->queryAll();
    
    $_ids = array();
    
    foreach($_items as $item)
    {
    $ids = split(",",$item["ids"]);
    array_shift($ids);
    $_ids[] = $ids[0];
    }
    //var_dump($_ids);
    foreach($_ids as $_id)
    {
    $item = Item::findOne($_id);
    $item->delete();
    }*/
    //clear duplicate rows
    /*$sql = "SELECT * FROM lv_items WHERE customer=178 AND serial <> '' AND serial IS NOT NULL GROUP BY location";
     $_items = Yii::$app->db->createCommand($sql)->queryAll();
    //var_dump($count = Item::find()->where(['customer'=>178, 'location'=>$_existing_location, 'serial'=>$_existing_serial])->count());
    foreach($_items as $item)
    {
    //$items = Item::find()->where(['customer'=>178, 'location'=>$item['location']])->all();
    $sql = "SELECT serial, COUNT(*) c FROM lv_items WHERE customer=178 AND location=".$item['location']." AND serial <> '' AND serial IS NOT NULL GROUP BY serial HAVING c > 1";
    $items = Yii::$app->db->createCommand($sql)->queryAll();
    //var_dump($items);
    foreach($items as $__item)
    {
    //var_dump($items);
    if(!empty($__item['serial']))
    {
    $_existing_serial = $__item['serial'];
    $count = $__item['c'];
    
    //$count = Item::find()->where(['customer'=>178, 'serial'=>$_existing_serial])->count();
    	
    if($count > 1 )
    {
    $count_without_one_item = $count - 1;
    //var_dump($count_without_one_item);
    if($count_without_one_item > 0)
    {
    $_find_others_items = Item::find()->where(['customer'=>178, 'serial'=>$_existing_serial])->limit($count_without_one_item)->all();
    //
    foreach($_find_others_items as $_find_others_item)
    {
    //var_dump($_find_others_item->id);
    $_find_others_item->delete();
    }
    }
    }
    }
    }
    }*/
    /*$items = ItemTagnumber::find()->all();
    
    foreach($items as $item)
    {
    $model = Item::find()->where(['serial'=>$item->serialnumber])->one();
    if($model !== null)
    {
    $model->tagnum = $item->tagnum;
    $model->save();
    }
    }*/
    
    /*$details = LocationDetailImport::find()->all();
    
    foreach($details as $detail)
    {
    $location = Location::find()->where(['storenum'=>$detail->storenum])->one();
    //
    if($location !== null)
    {
    $location->connection_type = $detail->connection_type;
    $location->notes = $detail->notes;
    $location->save();
    //
    $locationdetail = new LocationDetail;
    $locationdetail->locationid = $location->id;
    $locationdetail->ipaddress = $detail->ipaddress;
    $locationdetail->subnet_mask = $detail->subnet_mask;
    $locationdetail->gateway = $detail->gateway;
    $locationdetail->primary_dns = $detail->primary_dns;
    $locationdetail->secondary_dns = $detail->secondary_dns;
    $locationdetail->wins_server = $detail->wins_server;
    $locationdetail->save();
    }
    }*/
    
    /*$locations = LocationDelete::find()->all();
    
    foreach($locations as $location)
    {
    $location_parent = LocationParent::find()->where(['parent_code'=>$location->division_id])->one();
    $_find_location = Location::find()->innerJoin('lv_locations_classments', '`lv_locations_classments`.`location_id` = `lv_locations`.`id`')->where(['customer_id'=>178, 'storenum'=>$location->storenum, 'parent_id'=>$location_parent->id])->groupBy('location_id')->one();
    if($_find_location != null)
    {
    $_find_location->deleted = 1;
    $_find_location->save();
    }
    else
    {
    $_find_location = new Location;
    $_find_location->customer_id = 178;
    $_find_location->storenum = $location->storenum;
    $_find_location->storename = $location->storename;
    $_find_location->zipcode = $location->zipcode;
    $_find_location->deleted = 1;
    $_find_location->save();
    
    $locationclassment = new LocationClassment;
    $locationclassment->parent_id = $location_parent->id;
    $locationclassment->location_id = $_find_location->id;
    $locationclassment->save();
    }
    }*/
    
    /*$items = ItemLocation::find()->all();
    
    foreach($items as $item)
    {
    $storenum = $item->storenum;
    	
    if (strpos($storenum, 'AWGNH') !== false)
    {
    $storenum = str_replace('AWGNH', '', $storenum);
    $parent_id = 2;
    }
    if (strpos($storenum, 'AWGLOST') !== false)
    {
    $storenum = str_replace('AWGLOST', '', $storenum);
    $parent_id = 3;
    }
    if (strpos($storenum, 'CORP') !== false)
    {
    $storenum = str_replace('CORP', '', $storenum);
    $parent_id = 4;
    }
    if (strpos($storenum, 'GC') !== false)
    {
    $storenum = str_replace('GC', '', $storenum);
    $parent_id = 5;
    }
    if (strpos($storenum, 'KC') !== false)
    {
    $storenum = str_replace('KC', '', $storenum);
    $parent_id = 6;
    }
    if (strpos($storenum, 'SP') !== false)
    {
    $storenum = str_replace('SP', '', $storenum);
    $parent_id = 7;
    }
    if (strpos($storenum, 'OK') !== false)
    {
    $storenum = str_replace('OK', '', $storenum);
    $parent_id = 8;
    }
    if (strpos($storenum, 'GO') !== false)
    {
    $storenum = str_replace('GO', '', $storenum);
    $parent_id = 9;
    }
    if (strpos($storenum, 'SO') !== false)
    {
    $storenum = str_replace('SO', '', $storenum);
    $parent_id = 10;
    }
    if (strpos($storenum, 'FW') !== false)
    {
    $storenum = str_replace('FW', '', $storenum);
    $parent_id = 11;
    }
    if (strpos($storenum, 'VM') !== false)
    {
    $storenum = str_replace('VM', '', $storenum);
    $parent_id = 12;
    }
    if($storenum[0] == "0")
    	$storenum = substr($storenum, 1);
    	
    $__find_location = Location::find()->where(['customer_id'=>178, 'storenum'=>$storenum])->one();
    	
    $locationid = null;
    	
    if( $__find_location === null)
    {
    $_find_location = new Location;
    $_find_location->customer_id = 178;
    $_find_location->storenum = $storenum;
    $_find_location->save();
    
    $locationclassment = new LocationClassment;
    $locationclassment->parent_id = $parent_id;
    $locationclassment->location_id = $_find_location->id;
    $locationclassment->save();
    
    $locationid = $_find_location->id;
    }
    else
    	$locationid = $__find_location->id;
    	
    $model = new Item;
    $model->customer = 178;
    $model->serial = $item->serial;
    $model->location = $locationid;
    $model->model = $item->model;
    $model->tagnum = $item->tagnum;
    $model->status = 12;
    $model->shipped = date('Y-m-d H:i:s', strtotime($item->shipped));
    $model->save();
    //track item
    $itemlog = new Itemlog;
    $itemlog->userid = 2;
    $itemlog->status = 12;
    $itemlog->itemid = $model->id;
    $itemlog->locationid = $model->location;
    $itemlog->save();
    }*/
    
    /**/
    
    //$m__imp = $models_imported;
    
    //$locations_parents = LocationParent::find()->where('id > 1')->all();
    
    
    /*foreach($m__imp as $key=>$model_imported)
     {
    //remove division code.
    $location = new Location;
    $location->customer_id = 178;
    $location->storenum = $model_imported->storenum;
    $location->storename = $model_imported->storename;
    $location->address = $model_imported->address;
    $location->city = $model_imported->city;
    $location->state = $model_imported->state;
    $location->zipcode = $model_imported->zipcode;
    $location->save();
    }*/
}
