<?php
/**
 * Created by PhpStorm.
 * User: huawei
 * Date: 2017/6/19
 * Time: 9:41
 */

namespace common\lib\pay\alipay;


require_once("mobile/lib/alipay_rsa.function.php");
require_once("mobile/lib/alipay_core.function.php");
use yii\base\Object;

class PayMobile extends Object
{
    var $alipay_config;

    /**
     *支付宝移动支付网关（旧APP支付）
     */
    var $alipay_gateway_new = 'https://mapi.alipay.com/gateway.do?';

    /**
     * 生成要请求给支付宝的参数
     * @param $para_temp array 请求前的参数数组
     * @return bool|string
     */
    public function execute($para_temp)
    {
        //除去待签名参数数组中的空值和签名参数
        $para_filter = paraFilter($para_temp);

        //对待签名参数数组排序
        $para_sort = argSort($para_filter);

        //生成签名结果
        $mysign = rsaSign(createLinkstring($para_sort), $this->alipay_config['private_key_path']);

        //签名结果与签名方式加入请求提交参数组中
        $para_sort['sign'] = $mysign;
        $para_sort['sign_type'] = strtoupper(trim($this->alipay_config['sign_type']));

        return createLinkstringUrlencode($para_sort);
    }
}