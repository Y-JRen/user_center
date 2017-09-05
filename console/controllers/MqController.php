<?php
/**
 * Created by PhpStorm.
 * User: 雕
 * Date: 2017/8/25
 * Time: 16:32
 */

namespace console\controllers;

use cheframework\aliyunmq\HttpConsumer;
use common\models\PlatformOrder;
use Yii;
use yii\console\Controller;
use yii\helpers\ArrayHelper;

class MqController extends Controller
{
    /**
     * 订单中心的消息队列监听接口
     *
     * /usr/local/php/bin/php ./yii mq/order &
     *
     */
    public function actionListen()
    {
        $config = ArrayHelper::getValue(Yii::$app->params, 'MQ.orderCenter');
        $topic = ArrayHelper::getValue($config, 'topicIds.order');
        $url = ArrayHelper::getValue($config, 'url');
        $ak = ArrayHelper::getValue($config, 'ak');
        $sk = ArrayHelper::getValue($config, 'sk');
        $cid = ArrayHelper::getValue($config, 'cid');
        $consumer = new HttpConsumer($topic, $url, $ak, $sk, $cid);
        //启动消息订阅者
        $consumer->process(function ($message) {
            $jsonMessage = json_encode($message);
            $rootPath = dirname(Yii::$app->basePath) . '/';
            $command = '/usr/local/php/bin/php ' . $rootPath . 'yii mq/exec '; // php命令要用绝对路径 cron 脚本
            $command .= "'{$jsonMessage}' &"; //$jsonMessage 是json格式  需要用''包起来    & 很重要 有的话是异步  没有的话是同步
            pclose(popen($command, 'w'));
            return true;
        });
    }

    /**
     * 执行相关处理
     *
     * /usr/local/php/bin/php ./yii mq/exec
     *
     * @param $message
     * @return bool
     */
    public function actionExec($message)
    {
        $arrMessage = json_decode($message, true);

        /** 具体执行业务逻辑 */
        $action = ArrayHelper::getValue($arrMessage, ['body', 'mqAction']);
        if (strtoupper($action) == 'ADD') {
            // 获取orders ['id', 'uid']
            $id = ArrayHelper::getValue($arrMessage, ['body', 'orders', 'id']);
            $uid = ArrayHelper::getValue($arrMessage, ['body', 'orders', 'uid']);
            $model = new PlatformOrder();
            $model->uid = $uid;
            $model->platform_order_id = $id;
            $model->created_at = time();
            $model->save();
        }

        //操作完后删除消息体
        $config = ArrayHelper::getValue(Yii::$app->params, 'MQ.orderCenter');
        $topic = ArrayHelper::getValue($config, 'topicIds.order');
        $url = ArrayHelper::getValue($config, 'url');
        $ak = ArrayHelper::getValue($config, 'ak');
        $sk = ArrayHelper::getValue($config, 'sk');
        $cid = ArrayHelper::getValue($config, 'cid');
        $consumer = new HttpConsumer($topic, $url, $ak, $sk, $cid);
        $consumer->toldMqDelete(json_decode($message, true));
        return true;
    }
}