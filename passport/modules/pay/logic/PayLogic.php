<?php
/**
 * Created by PhpStorm.
 * User: xiongjun
 * Date: 2017/6/6
 * Time: 13:11
 */

namespace passport\modules\pay\logic;


use common\logic\Logic;
use passport\modules\pay\models\OrderForm;

class PayLogic extends Logic
{
    /**
     * @param OrderForm $order
     * @return  array
     */
    public function pay($order)
    {
        switch ($order->order_subtype) {
            case 'wechat_code':
                return WechatPayLogic::instance()->weChatPayCode($order);
                break;
            case 'wechat_jsapi':
                return WechatPayLogic::instance()->weChatPayJS($order);
                break;
            case 'alipay_pc':
            case 'alipay_wap':
                return AlipayLogic::instance()->pay($order);
                break;
            case 'line_down':// 线下支付只要生成订单
                return ['status' => 0];
                break;
        }
    }
}