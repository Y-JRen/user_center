<?php

namespace backend\controllers;

use backend\models\Order;
use backend\models\search\OrderLineSearch;
use common\helpers\JsonHelper;
use common\logic\HttpLogic;
use common\models\LogReview;
use common\models\PoolBalance;
use common\models\RechargeConfirm;
use moonland\phpexcel\Excel;
use passport\helpers\Config;
use Yii;
use backend\models\search\OrderSearch;
use yii\db\Exception;
use yii\helpers\ArrayHelper;
use yii\web\NotFoundHttpException;

/**
 * OrderController implements the CRUD actions for Order model.
 */
class OrderController extends BaseController
{
    public $history;

    /**
     * Lists all Order models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new OrderSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        if (Yii::$app->request->isPost) {
            Excel::export([
                'models' => $dataProvider->query->limit(10000)->all(),
                'mode' => 'export',
                'columns' => [
                    'user.phone',
                    [
                        'attribute' => 'platform',
                        'value' => function ($model) {
                            return ArrayHelper::getValue(Config::getPlatformArray(), $model->platform);
                        },
                    ],
                    'platform_order_id',
                    'order_id',
                    'type',
                    [
                        'attribute' => 'order_subtype',
                        'value' => function ($model) {
                            return ArrayHelper::getValue(Order::$subTypeName, $model->order_subtype, $model->order_subtype);
                        },
                    ],
                    'receipt_amount:currency',
                    'created_at:datetime',
                    'updated_at:datetime',
                    'orderStatus'
                ],
                'headers' => [
                    'created_at' => 'Date Created Content',
                ],
                'fileName' => '订单信息'
            ]);

            return $this->refresh();
        }

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * 线下充值待确认
     * @return mixed
     */
    public function actionLineDown()
    {
        $history = $this->history = $this->getShowHistory('recharge');
        $defaultParams = [
            'order_type' => Order::TYPE_RECHARGE,
            'order_subtype' => 'line_down',
        ];
        if (!$history) {
            $defaultParams['status'] = Order::STATUS_PENDING;
        }
        $queryParams = ArrayHelper::merge($defaultParams, Yii::$app->request->queryParams);

        $searchModel = new OrderLineSearch();
        $dataProvider = $searchModel->search($queryParams);


        if (Yii::$app->request->isPost) {
            Excel::export([
                'models' => $dataProvider->query->limit(10000)->all(),
                'mode' => 'export',
                'columns' => [
                    'user.phone',
                    [
                        'attribute' => 'platform',
                        'value' => function ($model) {
                            return ArrayHelper::getValue(Config::getPlatformArray(), $model->platform);
                        },
                    ],
                    [
                        'attribute' => '银行名称',
                        'value' => function ($model) {
                            return ArrayHelper::getValue(ArrayHelper::getValue(JsonHelper::BankHelper($model->remark), 'bankName'), 'value');
                        }
                    ],
                    [
                        'attribute' => '姓名',
                        'value' => function ($model) {
                            return ArrayHelper::getValue(ArrayHelper::getValue(JsonHelper::BankHelper($model->remark), 'accountName'), 'value');
                        }
                    ],
                    [
                        'attribute' => '流水单号',
                        'value' => function ($model) {
                            return ArrayHelper::getValue(ArrayHelper::getValue(JsonHelper::BankHelper($model->remark), 'referenceNumber'), 'value');
                        }
                    ],
                    [
                        'attribute' => 'order_subtype',
                        'value' => function ($model) {
                            return ArrayHelper::getValue(Order::$subTypeName, $model->order_subtype, $model->order_subtype);
                        },
                    ],
                    'receipt_amount:currency',
                    'created_at:datetime:申请时间',
                    'orderStatus'
                ],
                'headers' => [
                    'created_at' => 'Date Created Content',
                ],
                'fileName' => '打款确认'
            ]);

            return $this->refresh();
        }


        return $this->render('line-down', [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
        ]);
    }

    /**
     * 获取线下充值通过form
     * @param $id
     * @return string
     */
    public function actionLineDownPass($id)
    {
        return $this->renderPartial('_pass', ['model' => $this->findModel($id)]);
    }

    /**
     * 获取线下充值不通过form
     * @param $id
     * @return string
     */
    public function actionLineDownFail($id)
    {
        return $this->renderPartial('_fail', ['id' => $id]);
    }

    /**
     * 线下充值确认
     * @return \yii\web\Response
     */
    public function actionConfirmPass()
    {
        $post = Yii::$app->request->post();
        $model = $this->findModel($post['id']);
        if ($model->status !== $model::STATUS_PENDING) {
            Yii::$app->session->setFlash('error', '错误请求');
            return $this->refresh();
        }
        $db = Yii::$app->db->beginTransaction();
        try {
            $recharge = new RechargeConfirm();
            $recharge->order_id = $model->order_id;
            $recharge->org_id = $post['org_id'];
            $recharge->org = $post['org'];
            $recharge->account_id = $post['account_id'];
            $recharge->account = $post['account'];
            $recharge->type_id = $post['type_id'];
            $recharge->type = $post['type'];
            $recharge->back_order = $post['back_order'];
            $recharge->transaction_time = strtotime($post['transaction_time']);
            $recharge->remark = $post['remark'];
            $recharge->amount = $model->amount;
            $recharge->status = ($post['sync'] ? 1 : 2);
            $recharge->created_at = time();
            if (!$recharge->save()) {
                Yii::error(var_export($recharge->errors, true), 'actionConfirmPass');
                throw new Exception('确认失败，保存充值信息失败' . current($recharge->getFirstErrors()));
            }

            if (!$model->userBalance->plus($model->amount)) {
                throw new Exception('增加用户余额失败');
            }

            if (!$model->addPoolBalance(PoolBalance::STYLE_PLUS)) {
                throw new Exception('添加资金流水记录失败');
            }

            if (!$model->setOrderSuccess()) {
                throw new Exception('更新订单状态失败');
            }

            $db->commit();

            Yii::$app->session->setFlash('success', '确认成功');
        } catch (Exception $e) {
            $db->rollBack();
            Yii::$app->session->setFlash('error', $e->getMessage());
        }

        return $this->redirect(['line-down']);
    }

    /**
     * 线下充值，未到账确认
     * @return \yii\web\Response
     */
    public function actionConfirmFail()
    {
        $id = Yii::$app->request->post('id');
        $remark = Yii::$app->request->post('remark');
        $db = Yii::$app->db->beginTransaction();

        try {
            if (empty($remark)) {
                throw new Exception('备注不能为空');
            }

            $model = $this->findModel($id);
            if ($model->status !== $model::STATUS_PENDING) {
                throw new Exception('订单状态不允许执行此操作');
            }

            if (!$model->setOrderFail()) {
                throw new Exception(current($model->getFirstErrors()));
            }

            if (!$model->addLogReview($remark)) {
                throw new Exception('添加财务操作日志失败，请重试');
            }

            $db->commit();
            Yii::$app->session->setFlash('success', '操作成功');
        } catch (Exception $e) {
            $db->rollBack();
            Yii::$app->session->setFlash('error', $e->getMessage());
        }

        return $this->redirect(['line-down']);
    }

    /**
     * Displays a single Order model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        if (Yii::$app->request->isAjax) {
            return $this->renderPartial('view', [
                'model' => $this->findModel($id),
            ]);
        } else {
            return $this->render('view', [
                'model' => $this->findModel($id),
            ]);
        }
    }

    /**
     * 通过会员中心单号获取订单详情
     *
     * @param $orderId
     * @return string
     */
    public function actionDetail($orderId)
    {
        if (Yii::$app->request->isAjax) {
            return $this->renderPartial('view', [
                'model' => $this->findModelByOrderId($orderId),
            ]);
        } else {
            return $this->render('view', [
                'model' => $this->findModelByOrderId($orderId),
            ]);
        }
    }

    public function actionPlatform($platform_order_id)
    {
        if (Yii::$app->request->isAjax && !empty($platform_order_id)) {
            $path = Yii::$app->params['projects']['erp']['apiDomain'] . 'api/sale/detail';
            $params = ['onlineSaleNo' => $platform_order_id];
            $result = HttpLogic::instance()->http($path . '?' . http_build_query($params), 'GET');
            return $result;
        }

        return '请求方式有误\参数有误';
    }

    /**
     * Finds the Order model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Order the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Order::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    /**
     * Finds the Order model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $orderId
     * @return Order the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModelByOrderId($orderId)
    {
        if (($model = Order::find()->where(['order_id' => $orderId])->one()) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
