<?php
/**
 * Created by PhpStorm.
 * User: huawei
 * Date: 2017/6/19
 * Time: 18:05
 */

namespace backend\controllers;


use backend\models\Order;
use backend\models\search\OrderSearch;
use common\models\LogReview;
use common\models\PoolFreeze;
use common\models\User;
use Yii;
use yii\base\ErrorException;
use yii\web\NotFoundHttpException;

class CashController extends BaseController
{
    public function verbs()
    {
        return [
            'index' => 'GET',
            'confirm' => 'GET',
            'confirm-success' => 'POST',
            'confirm-fail' => 'POST',
        ];
    }

    /**
     * 提现待财务确认
     * @return mixed
     */
    public function actionIndex()
    {
        $queryParams['OrderSearch'] = ['order_type' => Order::TYPE_CASH, 'status' => Order::STATUS_PROCESSING];
        $searchModel = new OrderSearch();
        $dataProvider = $searchModel->search($queryParams);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * 财务确认
     */
    public function actionConfirm($id)
    {
        $model = $this->findModel($id);

        $user = User::findOne($model->uid);
        $queryParams['OrderSearch'] = [
            'status' => Order::STATUS_SUCCESSFUL,
            'uid' => $model->uid
        ];
        $searchModel = new OrderSearch();
        $dataProvider = $searchModel->search($queryParams);


        return $this->render('confirm', [
            'model' => $model,
            'dataProvider' => $dataProvider,
            'userModel' => $user
        ]);
    }

    /**
     * 财务审批通过
     * @param $id
     * @return \yii\web\Response
     * @throws ErrorException
     */
    public function actionConfirmSuccess($id)
    {
        $model = $this->findModel($id);

        if ($model->getFinanceConfirmCash()) {
            $transaction = Yii::$app->db->beginTransaction();
            try {
                $logModel = new LogReview(['order_id' => $id, 'order_status' => Order::STATUS_SUCCESSFUL]);
                $logModel->load(Yii::$app->request->post());
                if (!$logModel->save()) {
                    throw new ErrorException(print_r($logModel->errors, true));
                }

                if (!$model->setOrderSuccess()) {
                    throw new ErrorException('更新订单状态失败');
                }

                if (!$model->userFreeze->less($model->amount)) {
                    throw new ErrorException('更新用户冻结余额失败');
                }

                if (!$model->addPoolFreeze(PoolFreeze::STYLE_LESS)) {
                    throw new ErrorException('添加冻结资金流水记录失败');
                }

                $transaction->commit();
                Yii::$app->session->setFlash('success', '处理成功');
            } catch (ErrorException $e) {
                Yii::$app->session->setFlash('error', '处理失败');
                $transaction->rollBack();
                throw $e;
            }
        }

        return $this->redirect(['index']);
    }

    /**
     * 财务审批不通过
     *
     * 审批不通过，冻结金额返回到可用余额
     * @param $id
     * @return \yii\web\Response
     * @throws ErrorException
     */
    public function actionConfirmFail($id)
    {
        $model = $this->findModel($id);

        if ($model->getFinanceConfirmCash()) {
            $transaction = Yii::$app->db->beginTransaction();
            try {
                $logModel = new LogReview(['order_id' => $id, 'order_status' => Order::STATUS_FAILED]);
                $logModel->load(Yii::$app->request->post());
                if (!$logModel->save()) {
                    throw new ErrorException(print_r($logModel->errors, true));
                }

                if (!$model->setOrderFail()) {
                    throw new ErrorException('更新订单状态失败');
                }

                if (!$model->userFreeze->less($model->amount)) {
                    throw new ErrorException('更新用户冻结余额失败');
                }

                if (!$model->userBalance->plus($model->amount)) {
                    throw new ErrorException('更新用户可用余额失败');
                }
                $transaction->commit();
                Yii::$app->session->setFlash('success', '处理成功');
            } catch (ErrorException $e) {
                Yii::$app->session->setFlash('error', '处理失败');
                $transaction->rollBack();
                throw $e;
            }
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
            'status' => [Order::STATUS_SUCCESSFUL, Order::STATUS_FAILED, Order::STATUS_TRANSFER]
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