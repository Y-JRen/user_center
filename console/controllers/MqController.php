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
    private static $maxMqDealWithProcessNum = 8;//处理消息队列的进程数 最多允许同时存在8个进程

    /**
     * 订单中心的消息队列监听接口
     *
     * /usr/local/php/bin/php ./yii mq/exec &
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
            Yii::error(var_export($message, true));
            //进程数控制
            $this->checkMqDealWithProcessNum();

            $jsonMessage = json_encode($message);
            $rootPath = dirname(Yii::$app->basePath) . '/';
            $command = '/usr/local/php/bin/php ' . $rootPath . 'yii mq/exec '; // php命令要用绝对路径 cron 脚本
            $command .= "'{$jsonMessage}' &"; //$jsonMessage 是json格式  需要用''包起来    & 很重要 有的话是异步  没有的话是同步
            pclose(popen($command, 'w'));
            return true;
        });
    }

    /**
     * MQ进程数过高的时候应该等待进程处理之后降下来了再接收MQ消息
     */
    private function checkMqDealWithProcessNum()
    {
        $processNum = $this->getMqDealWithProcessNum();
        while (self::$maxMqDealWithProcessNum <= $processNum) {
            usleep(100);//停100微秒
        }
    }

    /**
     * 检查MQ进程数
     * @return mixed
     */
    private function getMqDealWithProcessNum()
    {
        $arrMqDealProcess = [
            'yii mq/exec',
        ];
        $strGrep = implode('|', $arrMqDealProcess);
        $cmd = "ps -ef | grep -E '{$strGrep}' | wc  -l ";
        $num = intval(shell_exec($cmd));
        return max(($num - 2), 0);
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
        $body = json_decode(ArrayHelper::getValue($arrMessage, 'body', ''), true);

        // 获取orders ['id', 'uid']
        $id = ArrayHelper::getValue($body, ['param', 'orders', 'id']);

        /* @var $model PlatformOrder */
        $model = PlatformOrder::find()->where(['platform_order_id' => $id])->one();
        if (!$model) {
            $model = new PlatformOrder();
            $model->platform_order_id = $id;
            $model->uid = ArrayHelper::getValue($body, ['param', 'orders', 'userId']);
            $model->platform_order_no = ArrayHelper::getValue($body, ['param', 'orders', 'no'], '');
            $model->create_time = intval(ArrayHelper::getValue($body, ['param', 'orders', 'createTime']) / 1000);
            $model->pro_name = ArrayHelper::getValue($body, ['param', 'cars', '0', 'itemCar', 'carName'], '');
            $model->pro_type = ArrayHelper::getValue($body, ['param', 'cars', '0', 'itemCar', 'carName'], '');
        }
        $model->status = ArrayHelper::getValue($body, ['param', 'orders', 'orderStatus']);

        if ($model->save()) {
            //操作完后删除消息体
            $config = ArrayHelper::getValue(Yii::$app->params, 'MQ.orderCenter');
            $topic = ArrayHelper::getValue($config, 'topicIds.order');
            $url = ArrayHelper::getValue($config, 'url');
            $ak = ArrayHelper::getValue($config, 'ak');
            $sk = ArrayHelper::getValue($config, 'sk');
            $cid = ArrayHelper::getValue($config, 'cid');
            $consumer = new HttpConsumer($topic, $url, $ak, $sk, $cid);
            $consumer->toldMqDelete(json_decode($message, true));
        } else {
            Yii::error(var_export($model->errors, true));
        }

        return true;
    }
}