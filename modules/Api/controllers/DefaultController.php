<?php

namespace app\modules\Api\controllers;

use yii\web\Controller;
use app\models\UserCreate;
use app\vendor\BlakeGardner\MacAddress;
use app\models\UserLogLocationTracking;
use app\models\UserLogTracking;
use app\models\Users;

class DefaultController extends Controller {

    const SECRET_KEY = "f25a2fc72690b780b2a14e140ef6a9e0";

    public function beforeAction($action) {
        $this->enableCsrfValidation = false;
        return parent::beforeAction($action);
    }

    public function actionAuth() {
        $post = \Yii::$app->request->post();
        $id = NULL;
//        $data = json_encode($postData);
//        print \Firebase\JWT\JWT::encode($data, self::SECRET_KEY); exit;
//        echo '<pre>'; print_r($post); exit;
        if (!isset($post['token'])) {
            $code = 'forbidden';
            $message = 'Token is required.';
        } else {
            //       $jwt = 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.IntcInNlY3JldFwiOlwidGVzdFwiLFwidXNlcm5hbWVcIjpcInRlc3RcIixcInBhc3N3b3JkXCI6XCJwYXNzd29yZFwifSI.k4ATsquIhamZmuPrbX0z3XBokBMU3OBkLgv5CyOW43Q';
            //       $jwt = \Firebase\JWT\JWT::encode($data, self::SECRET_KEY);
            $postData = \Firebase\JWT\JWT::decode($post['token'], self::SECRET_KEY, array('HS256'));
            $post = (array) json_decode($postData);
            $code = NULL;
            if (!isset($post['secret'])) {
                $code = 'forbidden';
                $message = '{secret} is not passed.';
            } else if (self::SECRET_KEY != trim($post['secret'])) {
                $code = 'forbidden';
                $message = 'Invalid Secret Key.';
            } else {
                $errors = [];
                if (!isset($post['username'])) {
                    $code = 'forbidden';
                    $errors [] = '{username}';
                }
                if (!isset($post['password'])) {
                    $code = 'forbidden';
                    $errors [] = '{password}';
                }
                if ($code != NULL) {
                    $message = implode(",", $errors) . ' is not passed.';
                } else {
                    $username = trim($post['username']);
                    $password = trim($post['password']);
                    if (empty($username)) {
                        $code = 'forbidden';
                        $errors [] = 'Username';
                    }
                    if (empty($password)) {
                        $code = 'forbidden';
                        $errors [] = 'Password';
                    }
                    if ($code != NULL) {
                        $message = implode(",", $errors) . ' is required.';
                    } else {
                        $model = UserCreate::find()->where('(username = :username or email = :username) and hash_password = :password', [':username' => $post['username'], ':password' => md5($post['password'])])->one();
                        if ($model == NULL) {
                            $code = 'forbidden';
                            $message = 'Invalid username or password.';
                        } else {
                            $id = $model->id;
                            if (YII_ENV_DEV) {
                                $location = $this->Iploc('68.115.162.38');
                            } else {
                                $location = $this->Iploc(\Yii::$app->getRequest()->getUserIP());
                            }
                            $user_tracking_location = new UserLogLocationTracking;
                            $user_tracking_location->continent_code = $location['geoplugin_continentCode'];
                            $user_tracking_location->contry_code = $location['geoplugin_countryCode'];
                            $user_tracking_location->country_name = $location['geoplugin_countryName'];
                            $user_tracking_location->region = $location['geoplugin_regionCode'];
                            $user_tracking_location->region_name = $location['geoplugin_regionName'];
                            $user_tracking_location->city = $location['geoplugin_city'];
                            $user_tracking_location->latitude = $location['geoplugin_latitude'];
                            $user_tracking_location->longitude = $location['geoplugin_longitude'];
                            $user_tracking_location->area_code = $location['geoplugin_areaCode'];
                            $user_tracking_location->dma_code = $location['geoplugin_dmaCode'];
                            $user_tracking_location->currency_code = $location['geoplugin_currencyCode'];
                            $user_tracking_location->currency_symbol = $location['geoplugin_currencySymbol'];
                            $user_tracking_location->save();
                            //
                            $user_tracking = new UserLogTracking;
                            $user_tracking->location_id = $user_tracking_location->id;
                            $user_tracking->mac_address = MacAddress::getCurrentMacAddress('eth0');
                            $user_tracking->ip_address = $_SERVER['REMOTE_ADDR'];
                            $user_tracking->real_ip_address = \Yii::$app->getRequest()->getUserIP();
                            $user_tracking->browser = '';
                            $user_tracking->using_proxy = (Users::isProxy() === true) ? 1 : 0;
                            $user_tracking->device_type = 'IOS';
                            $user_tracking->save();

                            $code = 'success';
                            $message = '';
                        }
                    }
                }
            }
        }
        $returnData = [
            'code' => $code,
            'message' => $message,
            'id' => $id
        ];
        echo \Firebase\JWT\JWT::jsonEncode(['token' => \Firebase\JWT\JWT::encode($returnData, self::SECRET_KEY)]);
    }

    private function Iploc($ip) {
        return unserialize(file_get_contents('http://www.geoplugin.net/php.gp?ip=' . $ip));
    }

}
