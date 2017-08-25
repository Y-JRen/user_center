<?php
/**
 * Created by PhpStorm.
 * User: legend
 * Date: 2017/8/25
 * Time: 上午9:45
 */

namespace common\traits;

use common\helpers\ConfigHelper;
use common\models\Order;
use common\models\PoolBalance;
use common\models\PoolFreeze;
use Exception;
use Yii;

/**
 * 消费相关
 *
 * Trait ConsumeTrait
 * @package common\traits
 */
trait ConsumeTrait
{
    /**
     * 添加手续费
     * @param Order $order 充值订单
     * @return bool
     */
    public function fee($order)
    {
        if ($order->counter_fee <= 0) {
            return true;
        }

        $model = new Order();
        $model->uid = $order->uid;
        $model->platform_order_id = $order->platform_order_id;
        $model->order_id = ConfigHelper::createOrderId();
        $model->order_type = Order::TYPE_CONSUME;
        $model->order_subtype = Order::SUB_TYPE_CONSUME_FEE;
        $model->amount = $order->counter_fee;
        $model->receipt_amount = $order->counter_fee;
        $model->status = Order::STATUS_PROCESSING;
        $model->desc = '手续费';
        $model->notice_status = 4;// 手续费的不需要通知第三方
        $model->platform = $order->platform;
        $model->quick_pay = 0;
        if ($model->save()) {
            $this->freeze($order);
            $this->thaw($order);
            return $model->setOrderSuccess();
        } else {
            Yii::error(var_export($model->errors, true), 'ConsumeTrait');
            return false;
        }
    }

    /**
     * 消费冻结步骤
     * @param Order $order 消费订单
     * @throws Exception
     */
    protected function freeze($order)
    {
        unset($order->userBalance, $order->userFreeze);
        if (!$order->userBalance->less($order->amount)) {
            throw new Exception('余额扣除失败');
        }

        if (!$order->addPoolBalance(PoolBalance::STYLE_LESS)) {
            throw new Exception('添加资金流水记录失败');
        }

        if (!$order->userFreeze->plus($order->amount)) {
            throw new Exception('资金冻结失败');
        }

        if (!$order->addPoolFreeze(PoolFreeze::STYLE_PLUS)) {
            throw new Exception('添加冻结资金流水记录失败');
        }

        if (!$order->setOrderProcessing()) {
            throw new Exception('更新消费订单状态失败');
        }
    }

    /**
     * 消费解冻
     * @param Order $order 消费订单
     * @throws Exception
     */
    protected function thaw($order)
    {
        unset($order->userFreeze);
        if (!$order->userFreeze->less($order->amount)) {
            throw new Exception('资金解冻失败');
        }

        if (!$order->addPoolFreeze(PoolFreeze::STYLE_LESS)) {
            throw new Exception('添加冻结资金流水记录失败');
        }

        if (!$order->setOrderSuccess()) {
            throw new Exception('更新消费订单状态失败');
        }
    }
}