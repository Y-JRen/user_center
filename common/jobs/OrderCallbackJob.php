<?php
/**
 * Created by PhpStorm.
 * User: huawei
 * Date: 2017/6/14
 * Time: 14:03
 */

namespace common\jobs;

use Yii;
use common\models\Order;
use passport\helpers\Config;
use yii\base\Object;
use yii\helpers\ArrayHelper;
use zhuravljov\yii\queue\Job;
use common\logic\HttpLogic;


/**
 * Class OrderCallbackJob
 * @package common\jobs
 */
class OrderCallbackJob extends Object implements Job
{
    public $notice_platform_param;
    public $order_id;
    public $platform_order_id;
    public $quick_pay;
    public $status;

    public function execute($queue)
    {
        $arrPost = [
            'notice_platform_param' => $this->notice_platform_param,
            'order_id' => $this->order_id,
            'platform_order_id' => $this->platform_order_id,
            'quick_pay' => $this->quick_pay,
            'status' => $this->status,
        ];
        echo json_encode($arrPost);

        /* @var $order Order */
        $order = Order::find()->where(['order_id' => $this->order_id])->one();
        if ($order) {
            $callbackUrl = Config::getOrderCallbackUrl($order->platform);
            $jsonRes = HttpLogic::instance()->http($callbackUrl, 'POST', $arrPost);

            echo $jsonRes;

            $arrRes = json_decode($jsonRes, true);

            /* @var $redis yii\redis\Connection */
            $redis = Yii::$app->redis;
            $key = "ORDER_CALLBACK:{$order->id}";

            if (ArrayHelper::getValue($arrRes, 'code') == '200') {
                $redis->del($key);

                // 通知对方平台成功
                $order->notice_status = 3;
                $order->save();
            } else {
                // 3秒后继续通知
                $errorNum = $redis->get($key);
                $redis->incr($key);
                if ($errorNum < 3) {
                    Yii::$app->queue_second->delay(3)->push($this);
                } elseif ($errorNum < 6) {
                    Yii::$app->queue_second->delay(5)->push($this);
                } else {// 超过6次就不处理
                    $redis->del($key);

                    // 通知对方平台失败，并不再处理
                    $order->notice_status = 2;
                    $order->save();
                }
            }
        } else {
            Yii::error('异常的队列数据:' . json_encode($arrPost), 'Queue');
        }
    }
}