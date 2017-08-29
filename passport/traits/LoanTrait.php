<?php
/**
 * Created by PhpStorm.
 * User: legend
 * Date: 2017/8/24
 * Time: 下午2:48
 */

namespace passport\traits;

use common\helpers\ConfigHelper;
use common\logic\RefundLogin;
use common\models\PoolBalance;
use common\models\PoolFreeze;
use Exception;
use passport\models\Order;
use Yii;

/**
 * 贷款相关
 *
 * 使用这两个方法，需要在外面加一层事物
 *
 * Trait LoanTrait
 * @package passport\traits
 */
trait LoanTrait
{
    /**
     * 贷款解冻
     * @param $uid
     * @param $orderIds
     * @param $amount
     * @return array
     */
    public function thaw($uid, $orderIds, $amount)
    {
        $result = ['status' => false];
        if ($errInfo = $this->errorCheck($uid, $orderIds, $amount)) {
            $result['info'] = $errInfo;
        } else {
            try {
                /* @var $orders Order[] */
                $orders = Order::find()->where(['order_id' => $orderIds])->all();

                foreach ($orders as $order) {
                    if (!$order->userFreeze->less($order->amount)) {
                        throw new Exception('用户冻结余额解冻失败');
                    }

                    if (!$order->addPoolFreeze(PoolFreeze::STYLE_LESS)) {
                        throw new Exception('添加冻结资金流水记录失败');
                    }

                    if (!$order->setOrderSuccess()) {
                        throw new Exception('更新订单状态失败');
                    }
                    unset($order);
                }
                $result['status'] = true;
            } catch (Exception $e) {
                $result['info'] = $e->getMessage();
            }
        }

        return $result;
    }

    /**
     * 贷款解冻的退款
     * @param $uid
     * @param $refundAmount
     * @param $platform_order_id
     * @return array
     */
    public function refund($uid, $refundAmount, $platform_order_id)
    {
        $result = ['status' => false];
        $data = ['amount' => $refundAmount, 'onlineSaleNo' => $platform_order_id];
        $confirm = RefundLogin::instance()->amountConfirm($data);
        if ($confirm) {
            try {
                // 添加退款
                $model = new Order();
                $model->order_id = ConfigHelper::createOrderId();
                $model->platform = ConfigHelper::getPlatform();
                $model->status = Order::STATUS_PENDING;
                $model->uid = $uid;
                $model->order_type = Order::TYPE_REFUND;
                $model->order_subtype = Order::SUB_TYPE_LOAN_REFUND;
                $model->amount = $refundAmount;
                $model->desc = '贷款退款';
                if (!$model->save()) {
                    Yii::error(var_export($model->errors, true), 'LoanTrait[refund]');
                    throw new Exception('生成退款订单失败:' . current($model->firstErrors));
                }

                if (!$model->userBalance->plus($refundAmount)) {
                    throw new Exception('用户余额增加失败');
                }

                if (!$model->addPoolBalance(PoolBalance::STYLE_PLUS)) {
                    throw new Exception('添加资金流水记录失败');
                }

                if (!$model->setOrderSuccess()) {
                    throw new Exception('更新贷款退款订单失败');
                }

                $result['status'] = true;
                $result['info'] = '冻结金额解冻并退款成功';
            } catch (Exception $e) {
                $result['info'] = $e->getMessage();
            }
        } else {
            $result['info'] = '解冻退款失败，退款金额有误';
        }

        return $result;
    }

    /**
     * 检测参数是否有错
     *
     * false:没有错误;
     * string:表示有错误
     *
     * @param $uid
     * @param $orderIds
     * @param $amount
     * @return bool|string
     */
    public function errorCheck($uid, $orderIds, $amount)
    {
        if (count($orderIds) > 1) {
            $totalAmount = Order::find()->where(['order_id' => $orderIds])->sum('amount');

            if ((double)$totalAmount != (double)$amount) {
                return '订单总金额不匹配';
            }

            /* @var $orders Order[] */
            $orders = Order::find()->where(['order_id' => $orderIds])->all();
            foreach ($orders as $order) {
                if ($order->status != Order::STATUS_PROCESSING) {
                    return '订单状态异常';
                }

                if ($order->uid != $uid) {
                    return '订单用户不匹配';
                }
            }
        } else {
            /* @var $order Order */
            $order = Order::find()->where(['order_id' => $orderIds])->one();
            if (!$order) {
                return '订单不存在';
            }

            if ($order->status != Order::STATUS_PROCESSING) {
                return '订单状态异常';
            }

            if ($order->uid != $uid || (double)$order->amount != (double)$amount) {
                return '订单用户、订单金额不匹配';
            }
        }

        return false;
    }
}