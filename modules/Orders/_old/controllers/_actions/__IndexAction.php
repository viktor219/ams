<?php
    /**
     * Lists all Order models.
     * @return mixed
     */

namespace app\modules\Orders\controllers\_actions;

use yii\base\Action;
use app\modules\Orders\models\Order;
use yii\web\NotFoundHttpException;

class IndexAction extends Action
{
    public function run($customer = 0)
    {
		return $this->controller->render('index', [
				'customer' => $customer
		]);
    }
}