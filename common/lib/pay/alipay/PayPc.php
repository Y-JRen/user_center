<?php

/**
 * Created by PhpStorm.
 * User: huawei
 * Date: 2017/6/7
 * Time: 18:25
 *
 * 支付宝PC网站支付
 */

namespace common\lib\pay\alipay;


class PayPc extends Core
{
    // PC网站接口名称
    public $method = 'alipay.trade.page.pay';

    //销售产品码，商家和支付宝签约的产品码
    public $product_code = 'FAST_INSTANT_TRADE_PAY';
}