<?php
/**
 * Created by PhpStorm.
 * User: huawei
 * Date: 2017/6/7
 * Time: 19:20
 */

namespace passport\modules\pay\logic;


use common\lib\pay\alipay\PayApp;
use common\lib\pay\alipay\PayCore;
use common\lib\pay\alipay\PayMobile;
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
     * 支付宝PC支付入口
     * @param $order OrderForm
     * @return string|array
     */
    public function pc($order)
    {
        $result = ['status' => 0, 'data' => ''];
        $subject = empty($order->desc) ? '支付宝充值' : $order->desc;

        $alipay = new PayCore(Config::getAlipayConfig());
        $pc = new PayPc();
        $pc->setSubject($subject);
        $pc->setTotalAmount($order->amount);
        $pc->setOutTradeNo($order->order_id);
        $html = $alipay->pagePay($pc, $order->return_url, Url::to('/alipay/notify', true));

        if ($html) {
            Redis::setAlipayOrderHtml($order->order_id, $html);
            $result['data']['url'] = Url::to(['/alipay/show', 'orderId' => $order->order_id], true);
        } else {
            $result['status'] = 2004;
        }

        return $result;
    }

    /**
     * 支付宝wap支付入口
     * @param $order OrderForm
     * @return string|array
     */
    public function wap($order)
    {
        $result = ['status' => 0, 'data' => ''];
        $subject = empty($order->desc) ? '支付宝充值' : $order->desc;

        $alipay = new PayCore(Config::getAlipayConfig());
        $wap = new PayWap();
        $wap->setSubject($subject);
        $wap->setTotalAmount($order->amount);
        $wap->setOutTradeNo($order->order_id);
        $html = $alipay->wapPay($wap, $order->return_url, Url::to('/alipay/notify', true));

        if ($html) {
            Redis::setAlipayOrderHtml($order->order_id, $html);
            $result['data']['url'] = Url::to(['/alipay/show', 'orderId' => $order->order_id], true);
        } else {
            $result['status'] = 2004;
        }

        return $result;
    }

    /**
     * 支付宝app支付入口
     * @param $order OrderForm
     * @return string|array
     */
    public function app($order)
    {
        $result = ['status' => 0, 'data' => ''];
        $subject = empty($order->desc) ? '支付宝充值' : $order->desc;

        $alipay = new PayCore(Config::getAlipayConfig());
        $app = new PayApp();
        $app->setSubject($subject);
        $app->setTotalAmount($order->amount);
        $app->setOutTradeNo($order->order_id);
        $html = $alipay->appPay($app, Url::to('/alipay/notify', true));

        if ($html) {
            $result['data']['html'] = $html;
        } else {
            $result['status'] = 2004;
        }

        return $result;
    }

    /**
     *
     * 支付宝旧版移动支付接口
     *
     * @param $order OrderForm
     * @return array
     */
    public function mobile($order)
    {
        $result = ['status' => 0, 'data' => ''];
        $subject = empty($order->desc) ? '支付宝充值' : $order->desc;
        $alipay_config = Config::getAlipayMobileConfig();

        $parameter = [
            "service" => $alipay_config['service'],
            "partner" => $alipay_config['partner'],
            "_input_charset" => $alipay_config['input_charset'],
            'sign_type' => $alipay_config['sign_type'],
            'notify_url' => Url::to('/alipay/mobile', true),
            'out_trade_no' => $order->order_id,
            'subject' => $subject,
            'payment_type' => 1,
            'seller_id' => $alipay_config['partner'],
            'total_fee' => $order->amount,
            'body' => $subject,
        ];

        $pay = new PayMobile(['alipay_config' => $alipay_config]);
        $html = $pay->execute($parameter);
        if ($html) {
            $result['data']['html'] = $html;
        } else {
            $result['status'] = 2004;
        }

        return $result;
    }

}