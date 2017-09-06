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
            'platform_order_id',
            'phone' => function ($model) {
                return ArrayHelper::getValue($model->user, 'phone');
            },
            'desc',
            'amount',// 订单金额
            'lackAmount',// 待充值金额
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
        $sum = (float)$this->getOrders()->sum('amount');

        if ($sum < $this->amount) {
            $amount = $this->amount - $sum;
        } else {
            // 快捷支付时，判断余额
            if ($this->quick_pay) {
                $balance = ArrayHelper::getValue($this->user, ['balance', 'amount'], 0);
                $amount = (($balance < $this->amount) ? $this->amount - $balance : 0);
            } else {
                // 充值的时候，不用管余额
                $amount = 0;
            }
        }
        return (float)$amount;
    }
}