<?php
/**
 * Created by PhpStorm.
 * User: huawei
 * Date: 2017/6/16
 * Time: 9:25
 */

namespace passport\modules\inside\controllers;


use common\models\PoolBalance;
use common\models\PoolFreeze;
use passport\modules\inside\models\Order;
use Yii;
use yii\base\Exception;

class OrderController extends BaseController
{
    /**
     * 贷款入账
     *
     * uid\platform_order_id\amount\desc
     *
     */
    public function actionLoan()
    {
        $model = new Order();
        $model->order_type = Order::TYPE_RECHARGE;
        $model->order_subtype = Order::SUB_TYPE_LOAN_RECORD;
        if ($model->load(Yii::$app->request->post(), '') && $model->save()) {// 创建贷款入账的充值订单
            $transaction = Yii::$app->db->beginTransaction();
            try {
                if (!$model->userBalance->plus($model->amount)) {
                    throw new Exception('添加用户可用余额失败');
                }

                if (!$model->addPoolBalance(PoolBalance::STYLE_PLUS)) {
                    throw new Exception('添加资金流水记录失败');
                }

                if (!$model->setOrderSuccess()) {
                    throw new Exception('更新贷款入账充值订单状态失败');
                }

                if (!$consumeModel = $model->createLoanConsumeOrder()) {// 创建消费订单
                    throw new Exception('创建消费订单失败');
                }

                if (!$consumeModel->userBalance->less($consumeModel->amount)) {
                    throw new Exception('扣除用户可用余额失败');
                }

                if (!$consumeModel->addPoolBalance(PoolBalance::STYLE_LESS)) {
                    throw new Exception('添加资金流水记录失败');
                }

                if (!$consumeModel->userFreeze->plus($consumeModel->amount)) {
                    throw new Exception('增加用户冻结余额失败');
                }

                if (!$consumeModel->addPoolFreeze(PoolFreeze::STYLE_PLUS)) {
                    throw new Exception('添加冻结资金流水记录失败');
                }

                if (!$consumeModel->setOrderProcessing()) {
                    throw new Exception('更新贷款入账消费订单状态失败');
                }

                $transaction->commit();
                return $this->_return(['order_id' => $consumeModel->order_id], 0, '贷款入账成功');
            } catch (Exception $e) {
                $transaction->rollBack();
                $model->setOrderFail();// 将订单设置为失败
                throw $e;
            }
        } else {
            return $this->_error(2401);
        }
    }

    /**
     * 贷款解冻
     * @return array
     * @throws Exception
     */
    public function actionUnfreeze()
    {
        $order_id = Yii::$app->request->post('order_id');
        $uid = Yii::$app->request->post('uid');
        $amount = Yii::$app->request->post('amount');

        /* @var $order Order */
        $order = Order::find()->where(['order_id' => $order_id])->one();
        if (!$order) {
            return $this->_error(2005, '订单不存在');
        }

        if ($order->status != Order::STATUS_PROCESSING) {
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

            if (!$order->addPoolFreeze(PoolFreeze::STYLE_LESS)) {
                throw new Exception('添加冻结资金流水记录失败');
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

}