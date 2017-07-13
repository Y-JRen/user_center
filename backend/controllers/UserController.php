<?php

namespace backend\controllers;

use backend\models\Order;
use backend\models\search\OrderSearch;
use common\models\PoolBalance;
use common\models\PoolFreeze;
use Yii;
use common\models\User;
use backend\models\search\UserSearch;
use yii\data\ActiveDataProvider;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * UserController implements the CRUD actions for User model.
 */
class UserController extends BaseController
{
    /**
     * Lists all User models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new UserSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single User model.
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
     * 用户订单交易明细
     * @param $uid
     * @return string
     */
    public function actionOrder($uid)
    {
        $user = $this->findModel($uid);
        $queryParams['OrderSearch'] = [
            'status' => Order::STATUS_SUCCESSFUL,
            'uid' => $uid
        ];
        $searchModel = new OrderSearch();
        $dataProvider = $searchModel->search($queryParams);
        return $this->render('order', [
            'dataProvider' => $dataProvider,
            'userModel' => $user
        ]);
    }

    public function actionAmount($uid)
    {
        $user = $this->findModel($uid);

        $balanceProvider = new ActiveDataProvider([
            'query' => PoolBalance::find()->where(['uid' => $uid])->orderBy('id desc'),
        ]);

        $freezeProvider = new ActiveDataProvider([
            'query' => PoolFreeze::find()->where(['uid' => $uid])->orderBy('id desc'),
        ]);

        return $this->render('amount', [
            'balanceProvider' => $balanceProvider,
            'freezeProvider' => $freezeProvider,
            'userModel' => $user
        ]);
    }

    /**
     * Finds the User model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return User the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = User::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
