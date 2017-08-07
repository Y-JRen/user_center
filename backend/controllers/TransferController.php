<?php
/**
 * Created by PhpStorm.
 * User: huawei
 * Date: 2017/6/19
 * Time: 18:05
 */

namespace backend\controllers;


use backend\models\Order;
use backend\models\search\OrderLineSearch;
use backend\models\search\OrderSearch;
use common\helpers\JsonHelper;
use common\models\TransferConfirm;
use moonland\phpexcel\Excel;
use passport\helpers\Config;
use Yii;
use yii\base\ErrorException;
use yii\helpers\ArrayHelper;
use yii\web\NotFoundHttpException;

class TransferController extends BaseController
{
    public $history;

    public function verbs()
    {
        return [
            'index' => 'GET',
            'confirm-form' => 'GET',
            'confirm-success' => 'POST',
        ];
    }

    /**
     * 提现待财务确认
     * @return mixed
     */
    public function actionIndex()
    {
        $history = $this->history = $this->getShowHistory('transfer');

        $defaultParams = ['order_type' => Order::TYPE_CASH];
        if ($history) {
            $defaultParams['status'] = [Order::STATUS_SUCCESSFUL, Order::STATUS_TRANSFER];
        } else {
            $defaultParams['status'] = Order::STATUS_SUCCESSFUL;
        }
        $queryParams = ArrayHelper::merge($defaultParams, Yii::$app->request->queryParams);

        $searchModel = new OrderLineSearch();
        $dataProvider = $searchModel->search($queryParams);

        if (Yii::$app->request->isPost) {
            Excel::export([
                'models' => $dataProvider->query->limit(10000)->all(),
                'mode' => 'export',
                'columns' => [
                    [
                        'attribute' => 'user.phone',
                        'value' => function ($model) {
                            return ' '.ArrayHelper::getValue($model->user, 'phone');
                        },
                    ],
                    [
                        'attribute' => 'platform',
                        'value' => function ($model) {
                            return ArrayHelper::getValue(Config::$platformArray, $model->platform);
                        },
                    ],
                    [
                        'attribute' => '到账银行名称',
                        'value' => function ($model) {
                            return ArrayHelper::getValue(ArrayHelper::getValue(JsonHelper::BankHelper($model->remark), 'bankName'), 'value');
                        }
                    ],
                    [
                        'attribute' => '到账银行卡',
                        'value' => function ($model) {
                            return ' '.ArrayHelper::getValue(ArrayHelper::getValue(JsonHelper::BankHelper($model->remark), 'bankCard'), 'value');
                        }
                    ],
                    'receipt_amount:currency',
                    'orderStatus',
                    'created_at:datetime:申请时间',
                    [
                        'attribute' => '审批人',
                        'value' => function ($model) {
                            return $model->cashUser;
                        }
                    ],
                ],
                'fileName' => '付款确认'
            ]);

            return $this->refresh();
        }

        return $this->render('index', [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
        ]);
    }

    /**
     * 财务确认
     */
    public function actionConfirmForm($id)
    {
        $model = $this->findModel($id);

        return $this->renderPartial('_modal', ['model' => $model]);
    }

    public function actionConfirmSuccess()
    {
        $post = Yii::$app->request->post();
        $model = $this->findModel($post['id']);
        if ($model->status !== $model::STATUS_SUCCESSFUL) {
            Yii::$app->session->setFlash('error', '错误的请求，该状态不可执行打款操作');
            return $this->refresh();
        }

        $db = Yii::$app->db->beginTransaction();
        try {
            $recharge = new TransferConfirm();
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
                Yii::error(var_export($recharge->errors, true), 'actionConfirmSuccess');
                throw new ErrorException('确认失败，保存打款信息失败' . current($recharge->getFirstErrors()));
            }

            if (!$model->setOrderTransfer()) {
                throw new ErrorException('更新订单状态失败');
            }

            $db->commit();
            Yii::$app->session->setFlash('success', '确认成功');
        } catch (ErrorException $e) {
            $db->rollBack();
            Yii::$app->session->setFlash('error', $e->getMessage());
        }

        return $this->redirect(['index']);
    }

    /**
     * 提现确认历史
     * @return mixed
     */
    public function actionHistory()
    {
        $queryParams['OrderSearch'] = [
            'order_type' => Order::TYPE_CASH,
            'status' => Order::STATUS_TRANSFER
        ];
        $searchModel = new OrderSearch();
        $dataProvider = $searchModel->search($queryParams);

        return $this->render('history', [
            'dataProvider' => $dataProvider,
        ]);
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
}