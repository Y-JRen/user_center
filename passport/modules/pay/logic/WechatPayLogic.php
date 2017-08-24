<?php
/**
 * Created by PhpStorm.
 * User: xiongjun
 * Date: 2017/6/8
 * Time: 09:45
 */

namespace passport\modules\pay\logic;


use common\lib\pay\wechat\PayCore;
use common\logic\Logic;
use common\models\Order;
use yii\helpers\Url;

class WechatPayLogic extends Logic
{
    /**
     * 微信支付, 统一下单入口
     *
     * @param Order $order
     * @param string $tradeType
     * @return array | string
     */
    public function weChatPay($order, $tradeType = 'NATIVE')
    {
        $pay = PayCore::instance($order->getWeChatConfig());
        $param = [
            'body' => '车城网充值中心',
            'out_trade_no' => $order->order_id,
            'spbill_create_ip' => $_SERVER['REMOTE_ADDR'],
            'total_fee' => $order->amount * 100,
            'notify_url' => 'http://' . $_SERVER['HTTP_HOST'] . Url::to(['/default/wechat-notify']),
            'trade_type' => $tradeType
        ];
        if ($tradeType == 'JSAPI') {
            $remark = json_decode($order->remark, true);
            $param['openid'] = $remark['openid'];
        }
        return $pay->unifiedOrder($param);

    }

    /**
     * JSSDK
     *
     * @param $order
     * @return array
     */
    public function weChatPayJS($order)
    {
        $result = $this->weChatPay($order, "JSAPI");
        if ($result['return_code'] == 'SUCCESS' && $result['result_code'] == 'SUCCESS') {
            $pay = PayCore::instance();
            $data = [
                'package' => 'prepay_id=' . $result['prepay_id'],
                'timeStamp' => time(),
                'nonceStr' => $pay->nonceStr(),
                'signType' => 'MD5',
                'appId' => $pay->weChatConfig['appid'],
            ];
            $data['paySign'] = $pay->sign($data);
            return [
                'data' => $data
            ];

        }
        return [
            'status' => 2002,
            'data' => $result
        ];
    }

    /**
     * 二维码支付
     *
     * @param $order
     * @return array
     */
    public function weChatPayCode($order)
    {
        $result = $this->weChatPay($order);
        if ($result['return_code'] == 'SUCCESS' && $result['result_code'] == 'SUCCESS') {
            $qrCode = 'http://' . $_SERVER['HTTP_HOST'] . Url::to(['/default/qrcode', 'url' => $result['code_url']]);
            return [
                'data' => [
                    'qrcode' => $qrCode,
                ],
                'status' => 0
            ];
        }
        return [
            'status' => 2002,
            'data' => $result
        ];
    }


    /**
     * APP支付
     * @param $order
     * @return array
     */
    public function weChatPayApp($order)
    {
        $result = $this->weChatPay($order);
        if ($result['return_code'] == 'SUCCESS' && $result['result_code'] == 'SUCCESS') {
            $pay = PayCore::instance();
            $data = [
                'appId' => $pay->weChatConfig['appid'],
                'partnerId' => $pay->weChatConfig['mch_id'],
                'prepayId' => $result['prepay_id'],
                'package' => 'Sign=WXPay',
                'noncestr' => $pay->nonceStr(),
                'timestamp' => time(),
            ];
            $data['sign'] = $pay->sign($data);
            return [
                'data' => $data
            ];
        }

        return [
            'status' => 2002,
            'data' => $result
        ];
    }
}