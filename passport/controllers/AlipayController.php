<?php

namespace passport\controllers;

use passport\helpers\Redis;
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
}