<?php

namespace backend\controllers;

use backend\models\Order;
use backend\models\search\OrderSearch;
use common\logic\HttpLogic;
use common\models\PoolBalance;
use common\models\PoolFreeze;
use common\models\UserBalance;
use common\models\UserFreeze;
use common\models\UserInfo;
use moonland\phpexcel\Excel;
use passport\helpers\Config;
use Yii;
use common\models\User;
use backend\models\search\UserSearch;
use yii\data\ActiveDataProvider;
use yii\data\Pagination;
use yii\helpers\ArrayHelper;
use yii\web\NotFoundHttpException;

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

        if (Yii::$app->request->isPost) {
            Excel::export([
                'models' => $dataProvider->query->limit(10000)->all(),
                'mode' => 'export',
                'columns' => [
                    [
                        'attribute' => 'phone',
                        'value' => function ($model) {
                            return ' '.$model->phone;
                        },
                    ],
                    [
                        'attribute' => 'from_platform',
                        'value' => function ($model) {
                            return ArrayHelper::getValue(Config::$platformArray, $model->from_platform);
                        },
                    ],
                    'reg_time:datetime',
                    'login_time:datetime',
                    'reg_ip',
                    [
                        'attribute' => 'status',
                        'value' => function ($model) {
                            return ArrayHelper::getValue(User::$statusArray, $model->status);
                        },
                    ],
                ],
                'fileName' => '用户信息'
            ]);

            return $this->refresh();
        }

        /* @var $balanceQuery yii\db\ActiveQuery */
        /* @var $freezeQuery yii\db\ActiveQuery */
        $balanceQuery = clone $dataProvider->query;
        $freezeQuery = clone $dataProvider->query;
        $totalBalance = $balanceQuery->innerJoin(UserBalance::tableName(), 'user.id=user_balance.uid')->sum('user_balance.amount');
        $totalFreeze = $freezeQuery->innerJoin(UserFreeze::tableName(), 'user.id=user_freeze.uid')->sum('user_freeze.amount');

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'totalBalance' => $totalBalance,
            'totalFreeze' => $totalFreeze,
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
        $defaultParams = [
            'uid' => $uid,
        ];
        $queryParams = ArrayHelper::merge($defaultParams, Yii::$app->request->queryParams);
        $searchModel = new OrderSearch();
        $dataProvider = $searchModel->search($queryParams);

        return $this->render('fund-record', [
            'dataProvider' => $dataProvider,
            'uid' => $uid,
        ]);
    }

    /**
     * 用户订单交易明细
     * @param $uid
     * @return string
     */
    public function actionView($uid)
    {
        $referrer = Yii::$app->request->referrer;
        str_replace('&reg', "& reg", $referrer);
        $referrer = htmlspecialchars($referrer);

        $needleArray = ['user/view', 'user/fund-record', 'user/order'];
        $isRecode = true;
        foreach ($needleArray as $needle) {
            if (stripos($referrer, $needle)) {
                $isRecode = false;
                break;
            }
        }

        if ($isRecode) {
            /* @var $redis yii\redis\Connection */
            $redis = Yii::$app->redis;
            $redis->set('returnHistory', $referrer);
            $redis->expire('returnHistory', 3600);
        }


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
     * 用户订单详情
     *
     * @param $uid
     * @return string
     */
    public function actionOrder($uid)
    {
        $path = Yii::$app->params['projects']['che.com']['apiDomain'] . 'api/account/order';
        $params = ['accountId' => $uid, 'pageIndex' => Yii::$app->request->get('page', 1), 'pagesize' => 20];

        $result = json_decode(HttpLogic::instance()->http($path . '?' . http_build_query($params), 'GET'), true);

        $detail = ArrayHelper::getValue($result, 'detail', []);
        $orderList = ArrayHelper::getValue($detail, 'orderList', []);

        $pagination = new Pagination(['totalCount' => ArrayHelper::getValue($detail, 'totalCount', 0)]);

        return $this->render('order', ['orderList' => $orderList, 'pagination' => $pagination]);
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
