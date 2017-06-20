<?php

namespace passport\controllers;

use common\lib\pay\alipay\PayCore;
use common\logic\ApiLogsLogic;
use passport\helpers\Config;
use passport\helpers\Redis;
use passport\modules\pay\logic\OrderLogic;
use Yii;

require_once(Yii::getAlias("@common/lib/pay/alipay/mobile/lib/alipay_notify.class.php"));

class AlipayController extends \yii\web\Controller
{
    public $enableCsrfValidation = false;

    /**
     * 订单失效后，跳转到支付宝页面
     * @param $orderId
     * @return mixed|\yii\web\Response
     */
    public function actionShow($orderId)
    {
        $html = Redis::getAlipayOrderHtml($orderId);
        if (empty($html)) {
            return $this->redirect('http://www.alipay.com');
        } else {
            echo $html;
            Yii::$app->end();
        }
    }

    /**
     * 支付宝异步回调返回地址接口
     */
    public function actionNotify()
    {
        $post = Yii::$app->request->post();
        ApiLogsLogic::instance()->addLogs('alipay', json_encode($post));

        $alipay = new PayCore(Config::getAlipayConfig());
        $result = $alipay->check($post);

        // 校验返回的参数是合法的
        if ($result) {
            $trade_status = Yii::$app->request->post('trade_status');//交易状态

            if (in_array($trade_status, ['TRADE_FINISHED', 'TRADE_SUCCESS'])) {// 交易结束，不可退款
                $result = OrderLogic::instance()->alipayNotify($post);
                if ($result) {
                    echo "success";
                    Yii::$app->end();
                }
            }
        }
        echo 'fail';
    }

    /**
     * 支付宝移动支付异步回调返回地址接口
     */
    public function actionMobile()
    {
        $post = Yii::$app->request->post();
        ApiLogsLogic::instance()->addLogs('alipay', json_encode($post));

        $alipay = new \AlipayNotify(Config::getAlipayMobileConfig());
        $result = $alipay->verifyNotify();

        // 校验返回的参数是合法的
        if ($result) {
            $trade_status = Yii::$app->request->post('trade_status');//交易状态

            if (in_array($trade_status, ['TRADE_FINISHED', 'TRADE_SUCCESS'])) {// 交易结束，不可退款
                $result = OrderLogic::instance()->alipayMobile($post);
                if ($result) {
                    echo "success";
                    Yii::$app->end();
                }
            }
        }
        echo 'fail';
    }
}