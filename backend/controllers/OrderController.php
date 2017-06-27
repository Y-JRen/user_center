<?php

namespace backend\controllers;

use backend\models\Order;
use common\logic\FinanceLogic;
use common\models\CompanyAccount;
use common\models\LogReview;
use common\models\RechargeConfirm;
use Yii;
use backend\models\search\OrderSearch;
use yii\base\ErrorException;
use yii\db\Exception;
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
        $queryParams['OrderSearch'] = [
            'order_type' => Order::TYPE_RECHARGE,
            'order_subtype' => 'line_down',
            'status' => Order::STATUS_PENDING
        ];
        $searchModel = new OrderSearch();
        $dataProvider = $searchModel->search($queryParams);
        return $this->render('line-down', [
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionLineDownForm($id)
    {
        /**
         * @var
         */
        $model = $this->findModel($id);
        $phone = \common\models\User::findOne($model->uid)->phone;

        return $this->renderPartial('_modal', ['model' => $model, 'phone' => $phone]);

    }

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
            $recharge->status = 1;
            $recharge->created_at = time();
            if (!$recharge->save()) {
                throw new Exception('确认失败，保存充值信息失败');
            }

            if (!$model->userBalance->plus($model->amount)) {
                throw new Exception('增加用户余额失败');
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
        }else{
            Yii::$app->session->setFlash('success', '操作失败'.json_encode($model->errors));
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
     * 完成财务确认动作
     * @param $id
     * @return string
     * @throws ErrorException
     */
    public function actionViewLineDown($id)
    {
        $model = $this->findModel($id);

        if ($model->isEdit && Yii::$app->request->isPost) {
            $transaction = Yii::$app->db->beginTransaction();
            try {
                $logModel = new LogReview(['order_id' => $id]);
                $logModel->load(Yii::$app->request->post());
                if (!$logModel->save()) {
                    throw new ErrorException(print_r($logModel->errors, true));
                }

                $model->status = $logModel->order_status;
                if (!$model->save()) {
                    throw new ErrorException('更新订单状态失败');
                }

                if ($model->isSuccessful) {
                    if (!$model->userBalance->plus($model->amount)) {
                        throw new ErrorException('更新用户余额失败');
                    }
                }
                $transaction->commit();
                Yii::$app->session->setFlash('success', '处理成功');
            } catch (ErrorException $e) {
                Yii::$app->session->setFlash('error', '处理失败');
                $transaction->rollBack();
                throw $e;
            }
        }

        return $this->render('view', [
            'model' => $model
        ]);
    }

    /**
     * 完成财务提现确认动作
     * @param $id
     * @return string
     * @throws ErrorException
     */
    public function actionViewCash($id)
    {
        $model = $this->findModel($id);

        if ($model->isEdit && Yii::$app->request->isPost) {
            $transaction = Yii::$app->db->beginTransaction();
            try {
                $logModel = new LogReview(['order_id' => $id]);
                $logModel->load(Yii::$app->request->post());
                if (!$logModel->save()) {
                    throw new ErrorException(print_r($logModel->errors, true));
                }

                $model->status = $logModel->order_status;
                if (!$model->save()) {
                    throw new ErrorException('更新订单状态失败');
                }

                if ($model->isSuccessful) {
                    if (!$model->userFreeze->less($model->amount)) {
                        throw new ErrorException('更新用户冻结余额失败');
                    }
                }
                $transaction->commit();
                Yii::$app->session->setFlash('success', '处理成功');
            } catch (ErrorException $e) {
                Yii::$app->session->setFlash('error', '处理失败');
                $transaction->rollBack();
                throw $e;
            }
        }

        return $this->render('view', [
            'model' => $model
        ]);
    }

    /**
     * Displays a single Order model.
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
