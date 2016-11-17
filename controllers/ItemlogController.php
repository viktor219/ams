<?php

namespace app\controllers;

use Yii;
use app\models\Itemlog;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use app\components\AccessRule;
use yii\filters\AccessControl;
use app\models\User;

/**
 * ItemlogController implements the CRUD actions for Itemlog model.
 */
class ItemlogController extends Controller
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
				'only' => [
					'index'
				],
				'rules' => [
					[
						'actions' => ['index'],
						'allow' => true,
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
    /**
     * Lists all Itemlog models.
     * @return mixed
     */
    public function actionIndex()
    {
        $dataProvider = new ActiveDataProvider([
            'query' => Itemlog::find(),
        ]);
/**
 *             'query' => Item::find()->select('shipmentnumber, model, lv_itemslog.status, lv_itemslog.userid, lv_itemslog.id')
	        		->join('INNER JOIN', 'lv_itemslog', 'lv_itemslog.itemid=lv_items.id')
        			->groupBy('model, shipmentnumber')
 */
        return $this->render('index', [
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Itemlog model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }
/*
    /**
     * Creates a new Itemlog model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     *
    public function actionCreate()
    {
        $model = new Itemlog();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing Itemlog model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     *
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing Itemlog model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     *
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }
*/
    /**
     * Finds the Itemlog model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Itemlog the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Itemlog::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
