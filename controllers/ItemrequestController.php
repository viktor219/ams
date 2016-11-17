<?php

namespace app\controllers;

use app\vendor\Uploader;
use app\models\Medias;
use app\models\Models;
use app\models\ItemRequested;

use Yii;

class ItemrequestController extends \yii\web\Controller
{
	const FILE_UPLOAD_PATH = "/uploads/";
	const UPLOAD_CUSTOM_PATH = "/uploads/models/";
	
    public function actionValidate()
    {
		$data = Yii::$app->request->post();
		$_new_media_id = 0;
        //var_dump(Yii::$app->request->post());
		//pictures 
		if (isset($_FILES["file"]['name']) && !empty($_FILES["file"]['name'])) {
			$_uploader = new Uploader(self::FILE_UPLOAD_PATH, self::UPLOAD_CUSTOM_PATH, 'image', 'm_', $_FILES["file"]);
			$_result = $_uploader->process();
			//var_dump($_result);
			if (is_array($_result)) {
				//save media
				$_uploaded_file_name = $_result['filename'];
				$media = new Medias();
				$media->filename = $_uploaded_file_name;
				$media->path = self::UPLOAD_CUSTOM_PATH;
				$media->type = 1;
				$media->save();
				$_new_media_id = $media->id;
			} else {

				$_message = '<div class="alert alert-danger"><strong>Failed!</strong>' . $_result . '</div>';
				Yii::$app->getSession()->setFlash('error', $_message);
			}
		}
		//remove instance
		$item_requested = ItemRequested::findOne($data['_requestid']);
		//save model 
		$model = new Models;
		$model->manufacturer = $data['rv_manufacturer'];
		$model->descrip = $data['rv_description'];
		$model->customer_id = $item_requested->customer_id;
		if($_new_media_id > 0)
			$model->image_id = $_new_media_id;
		$model->frupartnum = $data['rv_frupart'];
		$model->manpartnum = $data['rv_manpart'];
		$model->category_id = $data['rv_category'];
		$model->department = $data['rv_departement'];
		if($model->validate()) {
			if($model->save()) {
				if($item_requested!==null)
					$item_requested->delete();
				$_message = '<div class="alert alert-success"><strong>Success!</strong> Model is successfully validate!</div>';
				Yii::$app->getSession()->setFlash('success', $_message);					
			}
		} else {
			$errors = $model->errors;
			$_message = '<div class="alert alert-danger"><strong>Failed!</strong>' . $errors . '</div>';
			Yii::$app->getSession()->setFlash('error', $_message);
		}
		//
		//return $this->redirect(['/site/index']);
		return $this->redirect(Yii::$app->request->referrer);
    }
}
