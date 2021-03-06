<?php
/**
 * Created by PhpStorm.
 * User: huawei
 * Date: 2017/6/17
 * Time: 18:18
 */

namespace passport\modules\pay\controllers;

use common\logic\RefundLogin;
use passport\modules\pay\models\OrderForm;
use Yii;
use passport\controllers\AuthController;
use yii\base\Exception;

class FreezeController extends AuthController
{
    /**
     * 贷款解冻
     * @return array
     * @throws Exception
     */
    public function actionThaw()
    {
        $order_id = Yii::$app->request->post('order_id');
        $uid = Yii::$app->user->id;
        $amount = Yii::$app->request->post('amount');

        /* @var $order OrderForm */
        $order = OrderForm::find()->where(['order_id' => $order_id])->one();
        if (!$order) {
            return $this->_error(2005, '订单不存在');
        }

        if ($order->status != OrderForm::STATUS_PROCESSING) {
            return $this->_error(2005, '订单状态异常');
        }

        if ($order->uid != $uid || $order->amount != $amount) {
            return $this->_error(2005, '订单用户、订单金额不匹配');
        }

        $transaction = Yii::$app->db->beginTransaction();
        try {
            if (!$order->userFreeze->less($amount)) {
                throw new Exception('用户冻结余额解冻失败');
            }

            if (!$order->setOrderSuccess()) {
                throw new Exception('更新订单状态失败');
            }

            $transaction->commit();
            return $this->_return('冻结金额解冻成功');
        } catch (Exception $e) {
            $transaction->rollBack();
            throw $e;
        }
    }

    /**
     * 贷款解冻并退款
     * @return array
     * @throws Exception
     * @todo 前往erp验证退款金额是否正确
     */
    public function actionThawRefund()
    {
        $platform_order_id = Yii::$app->request->post('platform_order_id');
        $order_id = Yii::$app->request->post('order_id');
        $uid = Yii::$app->user->id;
        $amount = Yii::$app->request->post('amount');
        $refundAmount = Yii::$app->request->post('refund_amount');

        $data = ['amount' => $refundAmount, 'onlineSaleNo' => $platform_order_id];
        $result = RefundLogin::instance()->amountConfirm($data);
        if (!$result) {
            return $this->_error(2005, '解冻退款失败，退款金额有误');
        }

        /* @var $order OrderForm */
        $order = OrderForm::find()->where(['order_id' => $order_id])->one();
        if (!$order) {
            return $this->_error(2005, '订单不存在');
        }

        if ($order->status != OrderForm::STATUS_PROCESSING) {
            return $this->_error(2005, '订单状态异常');
        }

        if ($order->uid != $uid || $order->amount != $amount) {
            return $this->_error(2005, '订单用户、订单金额不匹配');
        }

        $transaction = Yii::$app->db->beginTransaction();
        try {
            if (!$order->userFreeze->less($amount)) {
                throw new Exception('用户冻结余额解冻失败');
            }

            if (!$order->setOrderSuccess()) {
                throw new Exception('更新订单状态失败');
            }

            // 添加退款
            $model = new OrderForm();
            $model->uid = $uid;
            $model->order_type = OrderForm::TYPE_REFUND;
            $model->order_subtype = OrderForm::SUB_TYPE_LOAN_REFUND;
            $model->amount = $refundAmount;
            $model->desc = '贷款退款';
            if (!$model->save()) {
                throw new Exception('生成贷款退款订单失败');
            }

            if (!$model->userBalance->plus($refundAmount)) {
                throw new Exception('用户余额增加失败');
            }

            if (!$model->setOrderSuccess()) {
                throw new Exception('更新贷款退款订单失败');
            }

            $transaction->commit();
            return $this->_return('冻结金额解冻并退款成功');
        } catch (Exception $e) {
            $transaction->rollBack();
            throw $e;
        }
    }
}