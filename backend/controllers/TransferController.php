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
use common\logic\FinanceLogic;
use common\models\CompanyAccount;
use common\models\LogReview;
use common\models\TransferConfirm;
use common\models\User;
use Yii;
use yii\base\ErrorException;
use yii\web\NotFoundHttpException;

class TransferController extends BaseController
{
    public function verbs()
    {
        return [
            'index' => 'GET',
            'confirm' => 'GET',
            'confirm-success' => 'POST',
        ];
    }

    /**
     * 提现待财务确认
     * @return mixed
     */
    public function actionIndex()
    {
        $queryParams['OrderSearch'] = ['order_type' => Order::TYPE_CASH, 'status' => Order::STATUS_SUCCESSFUL];
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
        $phone = User::findOne($model->uid)->phone;

        return $this->renderPartial('_modal', ['model' => $model, 'phone' => $phone]);
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
                throw new ErrorException('确认失败，保存打款信息失败');
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