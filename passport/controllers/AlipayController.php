<?php

namespace passport\controllers;

use common\lib\pay\alipay\PayCore;
use passport\helpers\Config;
use passport\helpers\Redis;
use passport\modules\pay\logic\OrderLogic;
use Yii;

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
        $alipay = new PayCore(Config::getAlipayConfig());
        $result = $alipay->check(Yii::$app->request->post());

        // 校验返回的参数是合法的
        if ($result) {
            $trade_status = Yii::$app->request->post('trade_status');//交易状态

            if (in_array($trade_status, ['TRADE_FINISHED', 'TRADE_SUCCESS'])) {// 交易结束，不可退款
                $result = OrderLogic::instance()->alipayNotify(Yii::$app->request->post());
                if ($result) {
                    echo "success";
                    Yii::$app->end();
                }
            }
        }
        echo 'fail';
    }
}