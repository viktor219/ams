<?php 
	namespace app\vendor;
		
	class PHelper
	{
		/**
		 * get string between two characters.
		 */
		static function stringBetween($var1="",$var2="",$pool){
			$temp1 = strpos($pool,$var1)+strlen($var1);
			$result = substr($pool,$temp1,strlen($pool));
			$dd=strpos($result,$var2);
			if($dd == 0){
				$dd = strlen($result);
			}
		
			return substr($result,0,$dd);
		}	
		
		static function generatePurchasingJson()
		{
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
						ON lv_models.department=lv_departements.id
						LEFT JOIN lv_partnumbers pn
						ON pn.model=lv_models.id";
				
			$sql .= " WHERE descrip <> '' AND assembly = 0 GROUP BY lv_models.id";
			
			$models = \Yii::$app->db->createCommand($sql)->queryAll();
			
			$output = array();
			
			$suggestions = array();
			
			foreach($models as $model)
			{
				$part_id = "";
				$model_aei = "";
				if(isset($model['partid']))
					$part_id = $model['partid'];
				$part_id = ($part_id!="") ? ' (' . $part_id . ')' : '';
				//
				$name = $model['name'];
				if(!empty($name)) {
					$suggestions['id']=$model['id'];
					$suggestions['name']=trim($name) . $part_id;
					$output[] = $suggestions;
				}
			}
			//store data in json file
			$name = '_models';
			$_storepath = "public/autocomplete/json/purchasing/$name.json";
			//
			if(!is_dir($_storepath) && file_exists($_storepath))
				unlink($_storepath);			
			//
			$fp = fopen($_storepath, 'w');
			fwrite($fp, json_encode($output));
			fclose($fp);		
		}

		static function generateNonSOCustomerJson($customer)
		{			
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
							ON lv_models.department=lv_departements.id LEFT JOIN lv_partnumbers pn ON pn.model=lv_models.id AND pn.customer=:customer WHERE descrip <> '' GROUP BY lv_models.id";
			
			$models = \Yii::$app->db->createCommand($sql, [':customer'=>$customer])->queryAll();
			
			$output = array();
			
			$suggestions = array();
			
			foreach($models as $model)
			{
				$part_id = "";
				if(isset($model['partid'])) {
					$part_id = $model['partid'];
				}
				$part_id = ($part_id!="") ? ' (' . $part_id . ')' : '';
				$model_aei = (!empty($model['aei'])) ? '(' . $model['aei'] . ') ' : '';
				$name = $model['name'];
				$suggestions['id']=$model['id'];
				$suggestions['name']=$model_aei . trim($name) . $part_id;
				$output[] = $suggestions;
			}
			//assemblies...
			$assemblies = \app\models\Models::find()->where('descrip <> ""')->andWhere('manufacturer IS NULL')->andWhere('assembly=1')->all();
			
			foreach($assemblies as $assembly)
			{
				$name = $assembly->descrip;
				$suggestions['id']=$assembly->id;
				$suggestions['name']=trim($name);
				$output[] = $suggestions;
			}
			//store data in json file
			$name = $customer . '_models';
			$_storepath = "public/autocomplete/json/receiving/$name.json";
			//
			if(!is_dir($_storepath) && file_exists($_storepath))
				unlink($_storepath);			
			//
			$fp = fopen($_storepath, 'w');
			fwrite($fp, json_encode($output));
			fclose($fp);			
		}
		
		static function generateModelsJson($_model)
		{
			$output = array();
				
			$suggestions = array();
			
			foreach (\app\models\Customer::find()->all() as $customer)
			{				
				$model = \app\models\Models::findOne($_model);
				
				$_partnumber  = \app\models\Partnumber::find()->where(['customer'=>$customer->id, 'model'=>$_model])->one();
				
				$_manufacturer = \app\models\Manufacturer::findOne($model->manufacturer);
				
				$name = $customer->id . '_models';
				
				$_storepath = "public/autocomplete/json/receiving/$name.json";		
				//
				$_old_content = file_get_contents($_storepath);
				
				$_start_new_content = substr($_old_content, 0, -1);
				
				$_start_new_content .= ",";
				
				if($model->assembly)
					$model_aei = '';
				else 
					$model_aei = (!empty($model->aei)) ? '(' . $model->aei . ') ' : '';
				
				$part_id = "";
				
				if(isset($_partnumber->partid))
					$part_id = $_partnumber->partid;

				$part_id = ($part_id!="") ? ' (' . $part_id . ')' : '';
				
				$name = $_manufacturer->name . ' ' . $model->descrip;
					
				$name = $model_aei . trim($name) . $part_id;
					
				$_start_new_content .= '{"id":"'.$model->id.'","name":"'.$name.'"}';
				
				$_start_new_content .= "]";
				
				if(!is_dir($_storepath) && file_exists($_storepath))
					unlink($_storepath);
				//
				$fp = fopen($_storepath, 'w');
				
				fwrite($fp, $_start_new_content);
				
				fclose($fp);				
			}
		}
		
		static function updateModelsJson($_model)
		{
			$output = array();
			
			$suggestions = array();
				
			foreach (\app\models\Customer::find()->all() as $customer)
			{
				$model = \app\models\Models::findOne($_model);
				
				$_partnumber  = \app\models\Partnumber::find()->where(['customer'=>$customer->id, 'model'=>$_model])->one();
			
				$_manufacturer = \app\models\Manufacturer::findOne($model->manufacturer);
			
				$name = $customer->id . '_models';
			
				$_storepath = "public/autocomplete/json/receiving/$name.json";
				
				$name = $_manufacturer->name . ' ' . $model->descrip;
				//
				$_old_content = file_get_contents($_storepath);
				
				$_find_old_content_name = self::getStringBetween($_old_content, '"id":"'.$model->id.'",', '}');
				
				$_find_replace_content = '{"id":"'.$model->id.'",'.$_find_old_content_name.'}';
				
				//var_dump($_find_replace_content);exit(1);
				
				$_old_content = str_replace($_find_replace_content, '', $_old_content);
			
				$_start_new_content = substr($_old_content, 0, -1);
			
				$_start_new_content .= ",";
			
				if($model->assembly)
					$model_aei = '';
				else
					$model_aei = (!empty($model->aei)) ? '(' . $model->aei . ') ' : '';		
				
				$part_id = "";
				
				if(isset($_partnumber->partid))
					$part_id = $_partnumber->partid;
				
				$part_id = ($part_id!="") ? ' (' . $part_id . ')' : '';
						
				$name = $model_aei . trim($name) . $part_id;
				
				$_start_new_content .= '{"id":"'.$model->id.'","name":"'.$name.'"}';
		
				$_start_new_content .= "]";
		
				if(!is_dir($_storepath) && file_exists($_storepath))
					unlink($_storepath);
				
				$_start_new_content = str_replace(',,', ',', $_start_new_content);
				//
				$fp = fopen($_storepath, 'w');
		
				fwrite($fp, $_start_new_content);
		
				fclose($fp);
			}			
		}
		
		static function deleteModelsJson($_model)
		{
			$output = array();
				
			$suggestions = array();
			
			foreach (\app\models\Customer::find()->all() as $customer)
			{
				$model = \app\models\Models::findOne($_model);
										
				$name = $customer->id . '_models';
					
				$_storepath = "public/autocomplete/json/receiving/$name.json";
				
				$_content = file_get_contents($_storepath);
			
				$_find_old_content_name = self::getStringBetween($_content, '"id":"'.$model->id.'",', '}');
			
				$_find_replace_content = '{"id":"'.$model->id.'",'.$_find_old_content_name.'}';
			
				//var_dump($_find_replace_content);exit(1);
			
				$_content = str_replace($_find_replace_content, '', $_content);
					
				if(!is_dir($_storepath) && file_exists($_storepath))
					unlink($_storepath);
				
				$_content = str_replace('[,', '[', $_content);
				
				$_content = str_replace(',]', ']', $_content);
				
				$_content = str_replace(',,', ',', $_content);
				//
				$fp = fopen($_storepath, 'w');
					
				fwrite($fp, $_content);
					
				fclose($fp);
			}			
		}
		
		static function getStringBetween($str,$from,$to)
		{
			$sub = substr($str, strpos($str,$from)+strlen($from),strlen($str));
			return substr($sub,0,strpos($sub,$to));
		}
		
		static function getMainModels()
		{
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
						ON lv_models.department=lv_departements.id
						LEFT JOIN lv_partnumbers pn
						ON pn.model=lv_models.id
						WHERE descrip <> '' AND assembly = 0 GROUP BY lv_models.id";	

			$models = \Yii::$app->db->createCommand($sql)->queryAll();
			
			$output = array();
			
			$suggestions = array();
			
			foreach($models as $model)
			{
				$part_id = "";
				$model_aei = "";
				if(isset($model['partid']))
					$part_id = $model['partid'];
				$part_id = ($part_id!="") ? ' (' . $part_id . ')' : '';
				$model_aei = ($model['aei']!="" && !empty($aei)) ? '(' . $model['aei'] . ') ' : '';
				//
				$name = $model['name'];
				if(!empty($name)) {
					$suggestions['id']=$model['id'];
					$suggestions['name']=$model_aei . trim($name) . $part_id;
					$output[] = $suggestions;
				}
			}
			//
			return $output;
		}
		
		static function GenerateRandomColor()
		{
			$color = '#';
			$colorHexLighter = array("9","A","B","C","D","E","F" );
			for($x=0; $x < 6; $x++):
				$color .= $colorHexLighter[array_rand($colorHexLighter, 1)]  ;
			endfor;
			return substr($color, 0, 7);
		}
	}

?>