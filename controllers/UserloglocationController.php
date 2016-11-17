<?php

namespace app\controllers;

use Yii;
use app\models\UserLogLocationTracking;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use app\components\AccessRule;
use yii\filters\AccessControl;
use app\models\User;

/**
 * UserloglocationController implements the CRUD actions for UserLogLocationTracking model.
 */
class UserloglocationController extends Controller
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
				'only' => ['index','create', 'update', 'view', 'delete'],
				'rules' => [
					[
						'actions' => ['index','create', 'update', 'view', 'delete'],
						'allow' => true,
					// Allow few users
						'roles' => [
							User::TYPE_ADMIN,
							User::TYPE_CUSTOMER_ADMIN,
							User::TYPE_BILLING,
							User::TYPE_SALES,
							User::TYPE_CUSTOMER,
                            User::TYPE_SHIPPING
						],
					]
				],
			]
		];
	}
    /**
     * Lists all UserLogLocationTracking models.
     * @return mixed
     */
    public function actionIndex()
    {
        $dataProvider = new ActiveDataProvider([
            'query' => UserLogLocationTracking::find(),
        ]);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single UserLogLocationTracking model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Deletes an existing UserLogLocationTracking model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['/userlog/index']);
    }

    /**
     * Finds the UserLogLocationTracking model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return UserLogLocationTracking the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = UserLogLocationTracking::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
