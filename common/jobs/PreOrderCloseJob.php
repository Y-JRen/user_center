<?php

namespace common\jobs;

use common\models\Order;
use common\models\PreOrder;
use yii\base\Object;
use zhuravljov\yii\queue\Job;


/**
 * Class OrderCloseJob
 * 关闭预处理订单任务
 *
 * @package common\jobs
 */
class PreOrderCloseJob extends Object implements Job
{
    public $id;// preOrder 主键

    public function execute($queue)
    {
        /* @var $model PreOrder */
        $model = PreOrder::find()->where(['id' => $this->id])->one();
        if ($model) {
            if (($model->status == Order::STATUS_PENDING)) {
                $model->close();
            } else {
                echo "[{$model->order_id}] pre order close fail, status {$model->status}";
            }
        } else {
            echo "[{$model->order_id}] pre order close fail, order_id not exist";
        }
    }
}