<?php
/**
 * Created by PhpStorm.
 * User: xiongjun
 * Date: 2017/6/6
 * Time: 13:11
 */

namespace passport\modules\pay\logic;


use common\logic\Logic;
use passport\modules\pay\models\OrderRecharge;

class PayLogic extends Logic
{
    /**
     * @param OrderRecharge $order
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
            case 'wechat_app':
                return WechatPayLogic::instance()->weChatPayApp($order);
                break;
            case 'alipay_pc':
                return AlipayLogic::instance()->pc($order);
                break;
            case 'alipay_wap':
                return AlipayLogic::instance()->wap($order);
                break;
            case 'alipay_app':
                return AlipayLogic::instance()->app($order);
                break;
            case 'alipay_mobile':
                return AlipayLogic::instance()->mobile($order);
                break;
            case 'lakala':
                return LakalaLogic::instance()->pay($order);
                break;
            case 'line_down':// 线下支付只要生成订单
                return ['status' => 0];
                break;
        }
    }
}