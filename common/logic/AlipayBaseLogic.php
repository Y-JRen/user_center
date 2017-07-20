<?php
/**
 * 支付宝的基础接口 关闭接口、查询接口
 * Created by PhpStorm.
 * User: huawei
 * Date: 2017/7/19
 * Time: 10:51
 */

namespace common\logic;


use common\lib\pay\alipay\PayClose;
use common\lib\pay\alipay\PayCore;
use common\lib\pay\alipay\PayQuery;
use passport\helpers\Config;
use Yii;
use yii\helpers\ArrayHelper;

class AlipayBaseLogic extends Logic
{
    /**
     * 关闭支付宝的支付订单
     * 关闭失败也无所谓，用户支付成功后，异步回调接口可以打开此类订单
     *
     * @param string $outTradeNo 支付宝的交易流水号
     * @param string $tradeNo 用户中心单号
     * @return bool
     */
    public function close($outTradeNo, $tradeNo = '')
    {
        $config = Config::getAlipayConfig();
        $pay = new PayCore($config);

        $object = new PayClose();
        $object->setTradeNo($tradeNo);
        $object->setOutTradeNo($outTradeNo);
        $result = $pay->close($object);

        ApiLogsLogic::instance()->addLogs('alipay_close.data', $result);
        return true;
    }

    /**
     * @param string $outTradeNo 支付宝的交易流水号
     * @param string $tradeNo 用户中心单号
     * @return bool
     */
    public function query($outTradeNo, $tradeNo = '')
    {
        $config = Config::getAlipayConfig();
        $pay = new PayCore($config);

        $object = new PayQuery();
        $object->setTradeNo($tradeNo);
        $object->setOutTradeNo($outTradeNo);
        $result = $pay->query($object);
        if (ArrayHelper::getValue($result, 'code') == '10000') {
            return $result;
        } else {
            return false;
        }
    }
}