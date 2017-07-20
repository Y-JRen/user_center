<?php
/**
 * Created by PhpStorm.
 * User: huawei
 * Date: 2017/7/14
 * Time: 14:12
 */

namespace passport\modules\pay\models;


use common\logic\AlipayBaseLogic;
use passport\models\Order;

class OrderClose extends Order
{
    // 允许关闭的子类型
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
     * 1、关闭第三方的订单
     * 2、关闭用户中心的订单
     * @return bool
     */
    public function close()
    {
        $status = true;
        switch ($this->order_subtype) {
            case self::SUB_TYPE_ALIPAY_APP:
            case self::SUB_TYPE_ALIPAY_PC:
            case self::SUB_TYPE_ALIPAY_WAP:
            case self::SUB_TYPE_ALIPAY_MOBILE:
                AlipayBaseLogic::instance()->close($this->order_id);
                break;
            case self::SUB_TYPE_WECHAT_CODE:
            case self::SUB_TYPE_WECHAT_JSAPI:
                // @todo 调用微信关闭接口
                break;
        }

        return true;
    }
}