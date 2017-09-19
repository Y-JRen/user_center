<?php
/**
 * Created by PhpStorm.
 * User: legend
 * Date: 2017/9/4
 * Time: 下午3:10
 */

namespace passport\modules\inside\models;


use yii\helpers\ArrayHelper;

class PreOrder extends \passport\models\PreOrder
{
    public function fields()
    {
        return [
            '_id' => function ($model) {
                return $model->id;
            },
            'order_id',
            'uid',
            'platform_order_id',
            'phone' => function ($model) {
                return ArrayHelper::getValue($model->user, 'phone');
            },
            'desc',
            'amount',// 订单金额
            'lackAmount',// 待充值金额
            'quick_pay' => function ($model) {
                return (boolean)$model->quick_pay;
            },
            'status',
            'created_at'
        ];
    }

    /**
     * 待充值金额
     * @return float
     */
    public function getLackAmount()
    {
        $floatSum = $this
            ->getOrders()
            ->andWhere(['order.status' => Order::STATUS_SUCCESSFUL, 'order.order_type' => Order::TYPE_RECHARGE])
            ->sum('amount');
        $sum = intval(floatval($floatSum) * 100) / 100;

        if ($sum < $this->amount) {
            $amount = $this->amount - $sum;
        } else {
            // 快捷支付时，判断余额
            if ($this->quick_pay) {
                $balance = ArrayHelper::getValue($this->user, ['balance', 'amount'], 0);
                $amount = (($balance < $this->amount) ? $this->amount * 100 - $balance * 100 : 0) / 100;
            } else {
                // 充值的时候，不用管余额
                $amount = 0;
            }
        }
        return $amount;
    }
}