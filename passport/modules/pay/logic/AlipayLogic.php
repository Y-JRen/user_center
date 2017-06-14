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
use passport\helpers\Config;
use passport\helpers\Redis;
use passport\logic\Logic;
use passport\modules\pay\models\OrderForm;
use yii\helpers\Url;

class AlipayLogic extends Logic
{
    /**
     * 支付宝支付入口
     * @param $order OrderForm
     * @return string|array
     */

    public function pay($order)
    {
        $result = ['status' => 0, 'data' => ''];
        $config = Config::getAlipayConfig();
        $subject = empty($order->desc) ? '支付宝充值' : $order->desc;

        $notifyUrl = Url::to('/alipay/notify', true);

        switch ($order->order_subtype) {
            case 'alipay_pc':
                $alipay = new PayCore($config);

                $pc = new PayPc();
                $pc->setSubject($subject);
                $pc->setTotalAmount($order->amount);
                $pc->setOutTradeNo($order->order_id);

                $html = $alipay->pagePay($pc, $order->return_url, $notifyUrl);
                break;
            case 'alipay_wap':
                $alipay = new PayCore($config);

                $wap = new PayWap();
                $wap->setSubject($subject);
                $wap->setTotalAmount($order->amount);
                $wap->setOutTradeNo($order->order_id);

                $html = $alipay->wapPay($wap, $order->return_url, $notifyUrl);
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