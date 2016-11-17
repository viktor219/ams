<?php

namespace app\controllers;

use Yii;
use app\models\UserLogTracking;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use app\components\AccessRule;
use yii\filters\AccessControl;
use app\models\User;
use app\models\Users;

/**
 * UserlogController implements the CRUD actions for UserLogTracking model.
 */
class UserlogController extends Controller
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
     * Lists all UserLogTracking models.
     * @return mixed
     */
    public function actionIndex($id=null)
    {
		$user = $this->findUser($id);
		//
		$query = UserLogTracking::find();
		if($user!==null)
			$query = UserLogTracking::find()->where(['userid'=>$user->id]);
		else 
			$query = UserLogTracking::find();
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        	'sort'=> ['defaultOrder' => ['created_at'=>SORT_DESC]]
        ]);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
			'user' => $user
        ]);
    }

    /**
     * Displays a single UserLogTracking model.
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
     * Deletes an existing UserLogTracking model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the UserLogTracking model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return UserLogTracking the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = UserLogTracking::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
	
    protected function findUser($id)
    {
        if (($model = Users::findOne($id)) !== null) {
            return $model;
        } else {
            return null;
        }
    }
}
