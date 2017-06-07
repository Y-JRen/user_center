<?php
/**
 * Created by PhpStorm.
 * User: huawei
 * Date: 2017/6/7
 * Time: 19:20
 */

namespace passport\modules\pay\logic;


use common\lib\pay\alipay\PayPc;
use common\lib\pay\alipay\PayWap;
use common\models\Order;
use passport\helpers\Config;
use passport\logic\Logic;

class AlipayLogic extends Logic
{
    /**
     * 支付宝支付入口
     * @param $order Order
     * @return string|array
     */

    public function pay($order)
    {
        $config = Config::getAlipayConfig();
        $params = $this->setParam($order);

        switch ($order->order_subtype) {
            case 'alipay_pc':
                $pc = new PayPc($config);
                $html = $pc->pay($params);
                break;
            case 'alipay_wap':
                $wap = new PayWap($config);
                $html = $wap->pay($params);
                break;
            default:
                $html = ['status' => 2004];
        }
        return $html;
    }

    /**
     * 设置支付宝充值的参数
     * @param $order Order
     * @return array
     */
    protected function setParam($order)
    {
        $result['out_trade_no'] = $order->platform_order_id;
        $result['amount'] = $order->amount;
        $result['subject'] = $order->desc ? $order->desc : "支付宝充值{$order->amount}";
        return $result;
    }

}