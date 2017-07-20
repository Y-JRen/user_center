<?php
/**
 * Created by PhpStorm.
 * User: huawei
 * Date: 2017/7/14
 * Time: 14:12
 */

namespace passport\modules\pay\models;


use common\lib\pay\wechat\PayCore;
use common\logic\AlipayBaseLogic;
use passport\models\Order;

class OrderClose extends Order
{
    // 允许关闭的订单子类型
    public static $allowCloseSubtype = [
        self::SUB_TYPE_ALIPAY_MOBILE,
        self::SUB_TYPE_ALIPAY_PC,
        self::SUB_TYPE_ALIPAY_APP,
        self::SUB_TYPE_ALIPAY_WAP,
        self::SUB_TYPE_WECHAT_JSAPI,
        self::SUB_TYPE_WECHAT_CODE,
        self::SUB_TYPE_LAKALA,
    ];

    /**
     * 1、关闭第三方的订单,记录日志，不管返回值
     * 2、关闭用户中心的订单
     * @return bool
     */
    public function close()
    {
        switch ($this->order_subtype) {
            case self::SUB_TYPE_ALIPAY_APP:
            case self::SUB_TYPE_ALIPAY_PC:
            case self::SUB_TYPE_ALIPAY_WAP:
            case self::SUB_TYPE_ALIPAY_MOBILE:
                AlipayBaseLogic::instance()->close($this->order_id);
                break;
            case self::SUB_TYPE_WECHAT_CODE:
            case self::SUB_TYPE_WECHAT_JSAPI:
                $data = ['out_trade_no' => $this->order_id];
                PayCore::instance()->close($data);
                break;
        }

        return $this->setOrderClose();
    }
}