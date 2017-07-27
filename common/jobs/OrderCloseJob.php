<?php

namespace common\jobs;

use passport\modules\pay\models\OrderClose;
use yii\base\Object;
use zhuravljov\yii\queue\Job;


/**
 * Class OrderCloseJob
 * 关闭充值订单任务
 *
 * @package common\jobs
 */
class OrderCloseJob extends Object implements Job
{
    public $order_id;

    public function execute($queue)
    {
        /* @var $order OrderClose */
        $order = OrderClose::find()->where(['order_id' => $this->order_id])->one();
        if ($order) {
            if (($order->status == OrderClose::STATUS_PENDING)) {
                $order->close();
            } else {
                echo "[{$order->order_id}] order close fail, status {$order->status}";
            }
        } else {
            echo "[{$order->order_id}] order close fail, order_id not exist";
        }
    }
}