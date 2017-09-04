<?php
/**
 * Created by PhpStorm.
 * User: legend
 * Date: 2017/9/4
 * Time: 下午2:47
 */

namespace common\jobs;


use common\models\Order;
use common\models\PreOrder;
use Yii;
use yii\base\Object;
use zhuravljov\yii\queue\Job;

class LakalaOrderJob extends Object implements Job
{
    public $platform_order_id;

    public function execute($queue)
    {
        $amount = Order::find()->where(['platform_order_id' => $this->platform_order_id, 'status' => Order::STATUS_SUCCESSFUL])->sum('amount');

        /* @var $preOrder PreOrder */
        $preOrder = PreOrder::find()->where(['id' => $this->platform_order_id, 'status' => PreOrder::STATUS_PENDING])->one();

        if (empty($preOrder)) {
            Yii::error('预处理订单不存在');
            return false;
        }

        if ((float)$amount >= (float)$preOrder->amount) {
            // 快捷支付
            if ($preOrder->quick_pay) {
                // @todo 添加消费， 消费后添加异步回调

            } else {// 充值
                // @todo 发送回调通知
            }

        }

    }
}