<?php

namespace passport\helpers;

use Yii;

/**
 * redis公共调用模块
 * Class Redis
 * @package passport\helpers
 */
class Redis
{
    public static function getAlipayReturnHtmlKey($orderId)
    {
        return "alipay_html_{$orderId}";
    }

    /**
     * 将支付宝返回的html存入缓存，通过订单号取出
     * @param $orderId
     * @param $html
     */
    public static function setAlipayOrderHtml($orderId, $html)
    {
        $key = self::getAlipayReturnHtmlKey($orderId);

        /* @var $redis yii\redis\Connection */
        $redis = Yii::$app->redis;
        $redis->set($key, $html);
        $redis->expire($key, Config::$orderHtmlExpire);
    }

    /**
     * 通过订单号取出html缓存
     * @param $orderId
     * @return mixed
     */
    public static function getAlipayOrderHtml($orderId)
    {
        $key = self::getAlipayReturnHtmlKey($orderId);
        return Yii::$app->redis->get($key);
    }
}