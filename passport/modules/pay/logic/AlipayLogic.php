<?php
/**
 * Created by PhpStorm.
 * User: huawei
 * Date: 2017/6/7
 * Time: 19:20
 */

namespace passport\modules\pay\logic;


use common\lib\pay\alipay\PayCore;
use common\lib\pay\alipay\PayPc;
use common\lib\pay\alipay\PayWap;
use common\models\Order;
use passport\helpers\Config;
use passport\helpers\Redis;
use passport\logic\Logic;
use yii\helpers\Url;

class AlipayLogic extends Logic
{
    /**
     * 支付宝支付入口
     * @param $order Order
     * @return string|array
     */

    public function pay($order)
    {
        $result = ['status' => 0, 'data' => ''];
        $config = Config::getAlipayConfig();

        switch ($order->order_subtype) {
            case 'alipay_pc':
                $alipay = new PayCore($config);

                $pc = new PayPc();
                $pc->setSubject($order->desc);
                $pc->setTotalAmount($order->amount);
                $pc->setOutTradeNo($order->order_id);

                $html = $alipay->pagePay($pc, $alipay->getReturnUrl(), $alipay->getNotifyUrl());
                break;
            case 'alipay_wap':
                $alipay = new PayCore($config);

                $wap = new PayWap();
                $wap->setSubject($order->desc);
                $wap->setTotalAmount($order->amount);
                $wap->setOutTradeNo($order->order_id);

                $html = $alipay->wapPay($wap, $alipay->getReturnUrl(), $alipay->getNotifyUrl());
                break;
        }

        if (isset($html) && $html) {
            Redis::setAlipayOrderHtml($order->order_id, $html);
            $result['data']['url'] = Url::to(['/alipay/show', 'orderId' => $order->order_id], true);
        } else {
            $result['status'] = 2004;
        }

        return $result;
    }

}