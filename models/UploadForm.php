<?php 
	namespace app\models;
	
	use Yii;
	use yii\base\Model;
	use yii\web\UploadedFile;
	use yii\imagine;
	use yii\imagine\Image;
	use Imagine\Gd;
	use Imagine\Image\Box;
	use Imagine\Image\BoxInterface;
	use yii\web\Session;
	
	class UploadForm extends Model
	{
	    /**
	     * @var UploadedFile[]
	     */
	    public $imageFiles;
	
	    public function rules()
	    {
	        return [
	            [['imageFiles'], 'file', 'skipOnEmpty' => false, 'extensions' => 'png, jpg, jpeg', 'maxFiles' => 4],
	        ];
	    }
	    
	    public function upload()
	    {
	        if ($this->validate()) { 
	            foreach ($this->imageFiles as $file) {
					$filename = $file->baseName . '.' . $file->extension;
					$dir = 'uploads/images/tmp/';
	                $file->saveAs($dir . $filename);
					/*Image::frame($dir . $filename)
					->thumbnail(new Box(800, 600))
					->save($dir . 'thumb_' . $filename, ['quality' => 90]);	*/
					//Image::getImagine()->open($dir . $filename)->thumbnail(new Box(800, 600))->save($dir . 'thumb_' . $filename , ['quality' => 90]);
					//
					//unlink($dir . '__' . $filename);
					//
					$session = Yii::$app->session;
					//$session->set('__user_picture', 'thumb_' . $filename);
					$session->set('__user_picture', $filename);
	            }
	            return true;
	        } else {
	            return false;
	        }
	    }
	}