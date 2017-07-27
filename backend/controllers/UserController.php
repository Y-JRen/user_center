<?php

namespace backend\controllers;

use backend\models\Order;
use backend\models\search\OrderSearch;
use common\models\PoolBalance;
use common\models\PoolFreeze;
use common\models\UserInfo;
use Yii;
use common\models\User;
use backend\models\search\UserSearch;
use yii\data\ActiveDataProvider;
use yii\data\Pagination;
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
     * 展示资金明细
     *
     * @param $uid
     * @return string
     */
    public function actionFundRecord($uid)
    {
        $dataProvider = new ActiveDataProvider(['query' => Order::find()->where(['uid' => $uid])]);
        $data = Order::find()->where(['uid' => $uid])->all();
        return $this->render('fund-record', [
            'dataProvider' => $dataProvider,
            'uid' => $uid,
            'data' => $data,
        ]);
    }

    /**
     * 用户订单交易明细
     * @param $uid
     * @return string
     */
    public function actionView($uid)
    {
        $user = $this->findModel($uid);
        $queryParams['OrderSearch'] = [
            'status' => [Order::STATUS_SUCCESSFUL, Order::STATUS_TRANSFER],
            'uid' => $uid
        ];
        $searchModel = new OrderSearch();
        $dataProvider = $searchModel->search($queryParams);

        $data = UserInfo::find()->where(['uid' => $uid])->one();

        return $this->render('view', [
            'dataProvider' => $dataProvider,
            'userModel' => $user,
            'data' => $data,
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
