<?php
/**
 * Created by PhpStorm.
 * User: huawei
 * Date: 2017/6/7
 * Time: 18:26
 *
 * 支付宝wap支付模块
 */

namespace common\lib\pay\alipay;


class PayWap extends Core
{
    // 手机网站接口名称
    public $method = 'alipay.trade.wap.pay';

    //销售产品码，商家和支付宝签约的产品码
    public $product_code = 'QUICK_WAP_PAY';

}