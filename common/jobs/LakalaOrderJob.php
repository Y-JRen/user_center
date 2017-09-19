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
use common\traits\ConsumeTrait;
use Exception;
use Yii;
use yii\base\Object;
use zhuravljov\yii\queue\Job;

class LakalaOrderJob extends Object implements Job
{
    use ConsumeTrait;
    public $platform_order_id;

    public function execute($queue)
    {

        $amount = Order::find()->where([
            'platform_order_id' => $this->platform_order_id,
            'status' => Order::STATUS_SUCCESSFUL
        ])->sum('amount');

        /* @var $preOrder PreOrder */
        $preOrder = PreOrder::find()->where([
            'id' => $this->platform_order_id,
            'status' => PreOrder::STATUS_PENDING
        ])->one();

        if ($preOrder) {
            if ((float)$amount >= (float)$preOrder->amount) {
                $status = 1;
                $db = Yii::$app->db->beginTransaction();
                try {
                    // 快捷支付
                    if (!empty($preOrder->quick_pay)) {
                        //添加消费
                        $this->quickPay($preOrder);
                    }

                    // 将充值、快捷支付订单设置为成功
                    if (!$preOrder->setSuccess()) {
                        throw new Exception('更新预处理订单状态失败');
                    }
                    $db->commit();
                } catch (Exception $e) {
                    // @todo 是否需要设置预充值订单为失败
                    $db->rollBack();
                    $status = 3;
                    Yii::error("预处理订单处理失败" . $e->getMessage());
                }

                // 发送回调通知
                Yii::$app->queue_second->push(new OrderCallbackJob([
                    'notice_platform_param' => $preOrder->notice_platform_param,
                    'order_id' => $preOrder->order_id,
                    'platform_order_id' => $preOrder->platform_order_id,
                    'quick_pay' => $preOrder->quick_pay,
                    'status' => $status,
                ]));
            }
        } else {
            echo "预处理订单不存在；订单号【{$this->platform_order_id}】";
        }

    }
}