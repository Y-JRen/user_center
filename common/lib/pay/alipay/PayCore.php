<?php
/**
 * 支付宝公共参数模块
 */

namespace common\lib\pay\alipay;

use yii\base\InvalidConfigException;
use yii\base\Object;

require_once 'AopSdk.php';

class PayCore extends Object
{
    //支付宝网关地址
    public $gateway_url = "https://openapi.alipay.com/gateway.do";

    //支付宝公钥
    public $alipay_public_key;

    //商户私钥
    public $private_key;

    //应用id
    public $appid;

    //编码格式
    public $charset = "UTF-8";

    public $token = NULL;

    //返回数据格式
    public $format = "json";

    //签名方式
    public $signtype = "RSA2";

    //接口名称
    public $method;

    public $product_code;

    public $timeout_express;

    public function init()
    {
        if (empty($this->appid)) {
            throw new InvalidConfigException("appid should not be NULL!");
        }
        if (empty($this->private_key)) {
            throw new InvalidConfigException("private_key should not be NULL!");
        }
        if (empty($this->alipay_public_key)) {
            throw new InvalidConfigException("alipay_public_key should not be NULL!");
        }
        if (empty($this->charset)) {
            throw new InvalidConfigException("charset should not be NULL!");
        }
        if (empty($this->gateway_url)) {
            throw new InvalidConfigException("gateway_url should not be NULL!");
        }
    }

    /**
     * @param $builder PayPc
     * @param $return_url
     * @param $notify_url
     * @return mixed
     */
    public function pagePay($builder, $return_url, $notify_url)
    {

        $biz_content = $builder->getBizContent();

        $request = new \AlipayTradePagePayRequest();

        $request->setNotifyUrl($notify_url);
        $request->setReturnUrl($return_url);
        $request->setBizContent($biz_content);

        // 首先调用支付api
        $response = $this->aopClientRequestExecute($request, true);
        return $response;
    }

    /**
     * alipay.trade.wap.pay
     * @param $builder PayWap 业务参数，使用buildmodel中的对象生成。
     * @param $return_url string 同步跳转地址，公网可访问
     * @param $notify_url string 异步通知地址，公网可以访问
     * @return $response 支付宝返回的信息
     */
    public function wapPay($builder, $return_url, $notify_url)
    {

        $biz_content = $builder->getBizContent();
        //打印业务参数
//		$this->writeLog($biz_content);

        $request = new \AlipayTradeWapPayRequest();

        $request->setNotifyUrl($notify_url);
        $request->setReturnUrl($return_url);
        $request->setBizContent($biz_content);

        // 首先调用支付api
        $response = $this->aopClientRequestExecute($request, true);
        // $response = $response->alipay_trade_wap_pay_response;
        return $response;
    }

    /**
     * alipay.trade.app.pay
     * @param $builder PayApp
     * @param $notify_url
     * @return bool|mixed|\SimpleXMLElement|string
     */
    public function appPay($builder, $notify_url)
    {
        $biz_content = $builder->getBizContent();

        $request = new \AlipayTradeAppPayRequest();

        $request->setNotifyUrl($notify_url);
        $request->setBizContent($biz_content);

        $response = $this->aopAppRequestExecute($request);
        return $response;
    }

    /**
     * alipay.trade.query (统一收单线下交易查询)
     * @param $builder PayQuery 业务参数，使用buildmodel中的对象生成。
     * @return $response 支付宝返回的信息
     */
    public function query($builder)
    {
        $biz_content = $builder->getBizContent();
        //打印业务参数
        /* @var $request \AlipayTradeQueryRequest */
        $request = new \AlipayTradeQueryRequest();
        $request->setBizContent($biz_content);

        $response = $this->aopClientRequestExecute($request);
        $response = $response->alipay_trade_query_response;
        return $response;
    }

    /**
     * alipay.trade.close (统一收单交易关闭接口)
     * @param $builder PayClose 业务参数，使用buildmodel中的对象生成。
     * @return $response 支付宝返回的信息
     */
    public function close($builder)
    {
        $biz_content = $builder->getBizContent();
        $request = new \AlipayTradeCloseRequest();
        $request->setBizContent($biz_content);

        $response = $this->aopclientRequestExecute($request);
        $response = $response->alipay_trade_close_response;
        return $response;
    }

    /**
     * sdkClient
     * @param $request \AlipayTradePagePayRequest | \AlipayTradeWapPayRequest | \AlipayTradeQueryRequest | \AlipayTradeCloseRequest 接口请求参数对象。
     * @param $ispage bool 是否是页面接口，电脑网站支付是页面表单接口。
     * @return $response 支付宝返回的信息
     */
    public function aopClientRequestExecute($request, $ispage = false)
    {
        $aop = new \AopClient ();
        $aop->gatewayUrl = $this->gateway_url;
        $aop->appId = $this->appid;
        $aop->rsaPrivateKey = $this->private_key;
        $aop->alipayrsaPublicKey = $this->alipay_public_key;
        $aop->apiVersion = "1.0";
        $aop->postCharset = $this->charset;
        $aop->format = $this->format;
        $aop->signType = $this->signtype;

        if ($ispage) {
            $result = $aop->pageExecute($request, "post");
        } else {
            $result = $aop->Execute($request);
        }

        return $result;
    }

    /**
     * sdk 调用
     * @param $request
     * @return string
     */
    public function aopAppRequestExecute($request)
    {

        $aop = new \AopClient ();
        $aop->gatewayUrl = $this->gateway_url;
        $aop->appId = $this->appid;
        $aop->rsaPrivateKey = $this->private_key;
        $aop->alipayrsaPublicKey = $this->alipay_public_key;
        $aop->apiVersion = "1.0";
        $aop->postCharset = $this->charset;
        $aop->format = $this->format;
        $aop->signType = $this->signtype;

        $response = $aop->sdkExecute($request);
        return htmlspecialchars($response);
    }

    public function getNotifyUrl()
    {
        return '';
    }

    public function getReturnUrl()
    {
        return '';
    }

    /**
     * 验签方法
     * @param array $arr 验签支付宝返回的信息，使用支付宝公钥。
     * @return boolean
     */
    public function check($arr)
    {
        $aop = new \AopClient();
        $aop->alipayrsaPublicKey = $this->alipay_public_key;
        $result = $aop->rsaCheckV1($arr, $this->alipay_public_key, $this->signtype);

        return $result;
    }
}
