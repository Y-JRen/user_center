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
use common\helpers\JsonHelper;
use common\models\LogReview;
use common\models\PoolBalance;
use common\models\PoolFreeze;
use Exception;
use moonland\phpexcel\Excel;
use passport\helpers\Config;
use Yii;
use yii\helpers\ArrayHelper;
use yii\web\NotFoundHttpException;

class CashController extends BaseController
{
    public $history;

    public function verbs()
    {
        return [
            'index' => 'GET',
            'pass' => 'POST',
            'fail' => 'POST',
        ];
    }

    /**
     * 提现待财务确认
     * @return mixed
     */
    public function actionIndex()
    {
        $history = $this->history = $this->getShowHistory('cash');

        $defaultParams = ['order_type' => Order::TYPE_CASH];
        if (!$history) {
            $defaultParams['status'] = Order::STATUS_PROCESSING;
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
                        'attribute' => '到账银行名称',
                        'value' => function ($model) {
                            return ArrayHelper::getValue(ArrayHelper::getValue(JsonHelper::BankHelper($model->remark), 'bankName'), 'value');
                        }
                    ],
                    [
                        'attribute' => '到账银行卡',
                        'value' => function ($model) {
                            return ArrayHelper::getValue(ArrayHelper::getValue(JsonHelper::BankHelper($model->remark), 'bankCard'), 'value');
                        }
                    ],
                    'orderStatus',
                    'receipt_amount:currency',
                    'created_at:datetime:申请时间',
                ],
                'headers' => [
                    'created_at' => 'Date Created Content',
                ],
                'fileName' => '提现审批'
            ]);

            return $this->refresh();
        }

        return $this->render('index', [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
        ]);
    }

    /**
     * 财务审批通过
     *
     * @param $id
     * @return \yii\web\Response
     * @throws Exception
     */
    public function actionPass($id)
    {
        $model = $this->findModel($id);

        if ($model->getFinanceConfirmCash()) {
            $transaction = Yii::$app->db->beginTransaction();
            try {
                if (!$model->setOrderSuccess()) {
                    throw new Exception('更新订单状态失败');
                }

                if (!$model->userFreeze->less($model->amount)) {
                    throw new Exception('更新用户冻结余额失败');
                }

                if (!$model->addPoolFreeze(PoolFreeze::STYLE_LESS)) {
                    throw new Exception('添加冻结资金流水记录失败');
                }

                if (!$model->addLogReview()) {
                    throw new Exception('添加财务操作日志失败，请重试');
                }

                $transaction->commit();
                Yii::$app->session->setFlash('success', '处理成功');
            } catch (Exception $e) {
                $transaction->rollBack();
                Yii::$app->session->setFlash('error', $e->getMessage());
            }
        }

        return $this->redirect(['index']);
    }

    /**
     * 获取审批不通过的form表单
     * @param $id
     * @return string
     */
    public function actionFailForm($id)
    {
        return $this->renderPartial('_fail', ['id' => $id]);
    }

    /**
     * 财务审批不通过
     *
     * 审批不通过，冻结金额返回到可用余额
     * @param $id
     * @return \yii\web\Response
     * @throws Exception
     */
    public function actionFail($id)
    {
        $model = $this->findModel($id);

        if ($model->getFinanceConfirmCash()) {
            $transaction = Yii::$app->db->beginTransaction();
            try {
                if (!$model->setOrderFail()) {
                    throw new Exception('更新订单状态失败');
                }

                if (!$model->userFreeze->less($model->amount)) {
                    throw new Exception('更新用户冻结余额失败');
                }

                if (!$model->addPoolFreeze(PoolFreeze::STYLE_LESS)) {
                    throw new Exception('添加冻结资金流水记录失败');
                }

                if (!$model->userBalance->plus($model->amount)) {
                    throw new Exception('更新用户可用余额失败');
                }

                if (!$model->addPoolBalance(PoolBalance::STYLE_PLUS)) {
                    throw new Exception('添加资金流水记录失败');
                }

                if (!$model->addLogReview(Yii::$app->request->post('remark'))) {
                    throw new Exception('添加财务操作日志失败，请重试');
                }

                $transaction->commit();
                Yii::$app->session->setFlash('success', '处理成功');
            } catch (Exception $e) {
                $transaction->rollBack();
                Yii::$app->session->setFlash('error', $e->getMessage());
            }
        }

        return $this->redirect(['index']);
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