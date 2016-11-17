<?php

namespace app\modules\Training\controllers;

use yii\web\Controller;

class DefaultController extends Controller
{
    const FOLDER_NAME = 'videos';

    public function actionIndex()
    {
//        $folderPath = $_SERVER['DOCUMENT_ROOT'] . self::FOLDER_NAME;
//        $videos = [];
//        if (file_exists($folderPath)) {
//            $dh = opendir($folderPath);
//            while (false !== ($filename = readdir($dh))) {
//                $ext = pathinfo($filename, PATHINFO_EXTENSION);
//                if ($filename != '.' && $filename != '..') {
//                    $videos[] = '/'.self::FOLDER_NAME.'/'.$filename;
//                }
//            }
//        }
        $videos = \app\models\TrainingVideos::find()->all();
        return $this->render('index', ['videos' => $videos]);
    }
}
