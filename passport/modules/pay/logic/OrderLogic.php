<?php
/**
 * Created by PhpStorm.
 * User: xiongjun
 * Date: 2017/6/6
 * Time: 09:39
 */

namespace passport\modules\pay\logic;


use common\jobs\OrderCallbackJob;
use common\jobs\RechargePushJob;
use common\models\PoolBalance;
use common\models\RechargeConfirm;
use passport\modules\pay\models\OrderForm;
use Yii;
use common\logic\Logic;
use common\models\Order;
use common\models\UserBalance;
use yii\db\Exception;
use yii\helpers\ArrayHelper;

/**
 * 订单类
 *
 * Class OrderLogic
 * @package passport\modules\pay\logic
 */
class OrderLogic extends Logic
{
    /**
     * 更新订单状态
     *
     * @param array $param
     *
     * @return boolean
     * @throws Exception
     */
    public function notify($param)
    {
        $orderId = ArrayHelper::getValue($param, 'out_trade_no');
        if (!$orderId) {
            return false;
        }

        $order = $this->findOrder($orderId);
        if (!$order) {
            return false;
        }

        $cashFee = ArrayHelper::getValue($param, 'total_fee');
        if ($order->amount * 100 == $cashFee) {
            $db = Yii::$app->db;
            $transaction = $db->beginTransaction();
            try {
                $status = 1;// 充值成功、快捷支付成功

                //充值成功
                if (!$order->setOrderSuccess()) {
                    throw new Exception('订单更新失败');
                }

                if (!$order->userBalance->plus($order->amount)) {
                    throw new Exception('余额更新失败');
                }

                if (!$order->addPoolBalance(PoolBalance::STYLE_PLUS)) {
                    throw new Exception('添加资金流水记录失败');
                }

                // 快捷支付， 直接消费
                if ($order->quick_pay) {
                    $result = $order->addQuickPayOrder();
                    $status = $result ? 1 : 3;// 快捷支付，终止成功，消费失败
                }

                $transaction->commit();

                // 异步回调通知平台
                Yii::$app->queue_second->push(new OrderCallbackJob([
                    'notice_platform_param' => $order->notice_platform_param,
                    'order_id' => $order->order_id,
                    'platform_order_id' => $order->platform_order_id,
                    'quick_pay' => $order->quick_pay,
                    'status' => $status,
                ]));

                // 添加充值到账的记录,并推送到财务系统
                Yii::$app->queue_second->push(new RechargePushJob([
                    'back_order' => ArrayHelper::getValue($param, 'transaction_id'),
                    'order_id' => $order->order_id,
                    'amount' => $order->amount,
                    'transaction_time' => ArrayHelper::getValue($param, 'time_end'),
                    'method' => 2,
                    'uid' => $order->uid
                ]));
                return true;
            } catch (Exception $e) {
                $transaction->rollBack();
                throw $e;
            }
        }
        return false;
    }

    /**
     * 支付宝回调
     *
     *
     * @param $params
     * @return bool
     * @throws Exception
     */
    public function alipayNotify($params)
    {
        $orderId = ArrayHelper::getValue($params, 'out_trade_no');// 商家订单号
        if (!$orderId) {
            return false;
        }

        $order = $this->findOrder($orderId);
        if (!$order) {
            return false;
        }

        $amount = ArrayHelper::getValue($params, 'total_amount');// 订单金额
        if ($order->amount == $amount) {
            $transaction = Yii::$app->db->beginTransaction();
            try {
                $status = 1;
                if (!$order->setOrderSuccess())// 更新订单状态
                {
                    throw new Exception('订单更新失败');
                }

                if (!$order->userBalance->plus($order->amount)) {
                    throw new Exception('余额添加失败');
                }

                if (!$order->addPoolBalance(PoolBalance::STYLE_PLUS)) {
                    throw new Exception('添加资金流水记录失败');
                }

                if ($order->quick_pay) {// 快捷支付
                    $res = $order->addQuickPayOrder();
                    $status = ($res ? 1 : 3);
                }

                $transaction->commit();

                // 异步回调通知平台
                Yii::$app->queue_second->push(new OrderCallbackJob([
                    'notice_platform_param' => $order->notice_platform_param,
                    'order_id' => $order->order_id,
                    'platform_order_id' => $order->platform_order_id,
                    'quick_pay' => $order->quick_pay,
                    'status' => $status,
                ]));

                // 添加充值到账的记录,并推送到财务系统
                Yii::$app->queue_second->push(new RechargePushJob([
                    'back_order' => ArrayHelper::getValue($params, 'trade_no'),
                    'order_id' => $order->order_id,
                    'amount' => $order->amount,
                    'transaction_time' => ArrayHelper::getValue($params, 'gmt_payment'),
                    'method' => 1,
                    'uid' => $order->uid
                ]));
                return true;
            } catch (Exception $e) {
                $transaction->rollBack();
                throw $e;
            }
        }
        return false;
    }

    /**
     * 支付宝移动支付（旧app）回调
     *
     *
     * @param $params
     * @return bool
     * @throws Exception
     */
    public function alipayMobile($params)
    {
        $orderId = ArrayHelper::getValue($params, 'out_trade_no');// 商家订单号
        if (!$orderId) {
            return false;
        }
        $order = $this->findOrder($orderId);
        if (!$order) {
            return false;
        }

        $amount = ArrayHelper::getValue($params, 'total_fee');// 订单金额
        if ($order->amount == $amount) {
            $transaction = Yii::$app->db->beginTransaction();
            try {
                $status = 1;
                if (!$order->setOrderSuccess())// 更新订单状态
                {
                    throw new Exception('订单更新失败');
                }

                if (!$order->userBalance->plus($order->amount)) {
                    throw new Exception('余额添加失败');
                }

                if (!$order->addPoolBalance(PoolBalance::STYLE_PLUS)) {
                    throw new Exception('添加资金流水记录失败');
                }

                if ($order->quick_pay) {// 快捷支付
                    $res = $order->addQuickPayOrder();
                    $status = ($res ? 1 : 3);
                }

                $transaction->commit();

                // 异步回调通知平台
                Yii::$app->queue_second->push(new OrderCallbackJob([
                    'notice_platform_param' => $order->notice_platform_param,
                    'order_id' => $order->order_id,
                    'platform_order_id' => $order->platform_order_id,
                    'quick_pay' => $order->quick_pay,
                    'status' => $status,
                ]));

                // 添加充值到账的记录,并推送到财务系统
                Yii::$app->queue_second->push(new RechargePushJob([
                    'back_order' => ArrayHelper::getValue($params, 'trade_no'),
                    'order_id' => $order->order_id,
                    'amount' => $order->amount,
                    'transaction_time' => ArrayHelper::getValue($params, 'gmt_payment'),
                    'method' => 1,
                    'uid' => $order->uid
                ]));
                return true;
            } catch (Exception $e) {
                $transaction->rollBack();
                throw $e;
            }
        }
        return false;
    }


    /**
     * 拉卡拉POS机回调
     * @param $post
     * @return bool
     * @throws Exception
     */
    public function lakalaNotify($post)
    {
        $params = json_decode(ArrayHelper::getValue($post, 'data'), true);
        $orderId = ArrayHelper::getValue($params, 'out_trade_no');// 商家订单号
        if (!$orderId) {
            return false;
        }

        $order = $this->findOrder($orderId);
        if (!$order) {
            return false;
        }

        $totalFee = (int)ArrayHelper::getValue($params, 'total_fee');// 订单金额
        $amount = $totalFee / 100;
        if ($order->amount <= $amount) {// 有手续费,所以可能小于
            $transaction = Yii::$app->db->beginTransaction();
            try {
                $status = 1;
                $order->receipt_amount = $amount;
                $order->counter_fee = ($amount - $order->amount);
                if (!$order->setOrderSuccess())// 更新订单状态
                {
                    throw new Exception('订单更新失败');
                }

                if (!$order->userBalance->plus($order->amount)) {
                    throw new Exception('余额添加失败');
                }

                if (!$order->addPoolBalance(PoolBalance::STYLE_PLUS)) {
                    throw new Exception('添加资金流水记录失败');
                }

                if (!$order->addFeeOrder()) {
                    throw new Exception('添加手续费消费订单失败');
                }

                if ($order->quick_pay) {// 快捷支付
                    $res = $order->addQuickPayOrder();
                    $status = ($res ? 1 : 3);
                }

                $transaction->commit();

                // 异步回调通知平台
                Yii::$app->queue_second->push(new OrderCallbackJob([
                    'notice_platform_param' => $order->notice_platform_param,
                    'order_id' => $order->order_id,
                    'platform_order_id' => $order->platform_order_id,
                    'quick_pay' => $order->quick_pay,
                    'status' => $status,
                ]));

                // 添加充值到账的记录,并推送到财务系统 @todo 拉卡拉pos机流水账号，其他参数未好
                Yii::$app->queue_second->push(new RechargePushJob([
                    'back_order' => ArrayHelper::getValue($params, 'transaction_id'),
                    'order_id' => $order->order_id,
                    'amount' => $order->amount,
                    'transaction_time' => ArrayHelper::getValue($params, 'time_end'),
                    'method' => 3,//拉卡拉
                    'uid' => $order->uid
                ]));

                return true;
            } catch (Exception $e) {
                $transaction->rollBack();
                Yii::error($e->getMessage());
                throw $e;
            }
        }

        return false;
    }

    /**
     * 获取订单信息，并检测订单状态
     * @param $orderId
     * @return array|bool|null|OrderForm
     */
    protected function findOrder($orderId)
    {
        $model = OrderForm::find()->where(['order_id' => $orderId])->one();
        if ($model && $model->status == OrderForm::STATUS_PENDING) {
            return $model;
        } else {
            return false;
        }
    }
}