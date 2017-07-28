<?php

namespace backend\controllers;

use backend\models\Order;
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
    public function actionLineDown_0()
    {
        $queryParams['OrderSearch'] = [
            'order_type' => Order::TYPE_RECHARGE,
            'order_subtype' => 'line_down',
            'status' => Order::STATUS_PENDING,
//            'created_at' => Yii::$app->request->get('created_at'),
//            'key' => Yii::$app->request->get('key'),
        ];
        $searchModel = new OrderSearch();
        $dataProvider = $searchModel->search($queryParams);
        return $this->render('line-down', [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
        ]);
    }

    //重写linedown方法
    public function actionLineDown()
    {
        $searchModel = new OrderSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        return $this->render('line-down', [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
        ]);
    }

    /**
     * AJAX获取确认充值表单
     * @param $id
     * @return string
     */
    public function actionLineDownForm($id)
    {

        $model = $this->findModel($id);
        $phone = ArrayHelper::getValue($model->user, 'phone');

        return $this->renderPartial('_modal', ['model' => $model, 'phone' => $phone]);
    }

    /**
     * 线下充值确认
     * @return \yii\web\Response
     */
    public function actionLineDownSave()
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
                throw new Exception('确认失败，保存充值信息失败' . json_encode($recharge->errors));
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
     * 确认充值失败
     * @return \yii\web\Response
     */
    public function actionConfirmFail()
    {
        $id = Yii::$app->request->get('id');
        $model = $this->findModel($id);
        if ($model->status !== $model::STATUS_PENDING) {
            Yii::$app->session->setFlash('error', '错误请求');
            return $this->redirect(['line-down']);
        }

        if ($model->setOrderFail()) {
            Yii::$app->session->setFlash('success', '操作成功');
        } else {
            Yii::$app->session->setFlash('success', '操作失败' . json_encode($model->errors));
        }

        return $this->redirect(['line-down']);
    }

    /**
     * 线下充值确认历史
     * @return mixed
     */
    public function actionLineDownLog()
    {
        $queryParams['OrderSearch'] = [
            'order_type' => Order::TYPE_RECHARGE,
            'order_subtype' => "line_down",
            'status' => [Order::STATUS_SUCCESSFUL, Order::STATUS_FAILED]
        ];
        $searchModel = new OrderSearch();
        $dataProvider = $searchModel->search($queryParams);

        return $this->render('line-down-log', [
            'dataProvider' => $dataProvider,
        ]);
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
