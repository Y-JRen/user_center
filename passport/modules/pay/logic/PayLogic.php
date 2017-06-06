<?php
/**
 * Created by PhpStorm.
 * User: xiongjun
 * Date: 2017/6/6
 * Time: 13:11
 */

namespace passport\modules\pay\logic;


use common\lib\pay\wechat\PayCore;
use common\logic\Logic;
use common\models\Order;
use dosamigos\qrcode\QrCode;
use yii\helpers\Url;

class PayLogic extends Logic
{
    /**
     * @param Order $order
     * @return  array
     */
    public function pay($order)
    {
        switch ($order->order_subtype)
        {
            case 1:
                return $this->weChatPay($order);
                break;
        }
    }

    /**
     * 微信支付
     *
     * @param Order $order
     * @return array | string
     */
    public function weChatPay($order)
    {
        $pay = PayCore::instance();
        $param = [
            'body' => '车城网充值中心',
            'out_trade_no' => $order->order_id,
            'spbill_create_ip' => $_SERVER['REMOTE_ADDR'],
            'total_fee' => $order->amount * 100,
            'notify_url' => 'http://'.$_SERVER['HTTP_HOST'].Url::to(['/pay/order/wechat-notify']),
            'trade_type' => 'NATIVE'


        ];
        $result = $pay->unifiedOrder($param);
        if($result['return_code'] == 'SUCCESS' && $result['result_code'] == 'SUCCESS') {
            $qrCode = 'http://'.$_SERVER['HTTP_HOST'].Url::to(['/default/qrcode', 'url' => $result['code_url']]);
            return [
                'order_id' => $order->order_id,
                'qrcode' => $qrCode,
                'platform_order_id' => $order->platform_order_id
            ];
        }
        return $result;
    }
}