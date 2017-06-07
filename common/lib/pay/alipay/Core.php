<?php
/**
 * 支付宝公共参数模块
 */

namespace common\lib\pay\alipay;

use yii\base\InvalidConfigException;
use yii\base\Object;

class Core extends Object
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
    public $sign_type = "RSA2";

    //接口名称
    public $method;

    //文件编码
    public $file_charset = "UTF-8";

    public $post_charset = "UTF-8";

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
     * @param $apiParams
     * @return string
     */
    public function pay($apiParams)
    {
        // 获取【业务请求参数】的集合
        $bizContent = $this->getBizContent($apiParams);

        //组装系统参数
        $sysParams["app_id"] = $this->appid;
        $sysParams["method"] = $this->method;
        $sysParams["format"] = $this->format;
        $sysParams["return_url"] = $this->getReturnUrl();
        $sysParams["charset"] = $this->charset;
        $sysParams["sign_type"] = $this->sign_type;
        $sysParams["timestamp"] = date("Y-m-d H:i:s");
        $sysParams["version"] = '1.0';
        $sysParams["notify_url"] = $this->getNotifyUrl();
        $sysParams['biz_content'] = json_encode($bizContent, JSON_UNESCAPED_UNICODE);

        // 获取要签名的内容
        $signContent = $this->getSignContent($sysParams);

        // 获取签名
        $sysParams["sign"] = $this->sign($signContent, $this->sign_type);

        return $this->buildRequestForm($sysParams);
        return $data;
    }

    /**
     * 需要公共参数外的所有参数
     * 前面4个是必选参数
     * @return $params array
     */
    public function getBizContent($params)
    {
        $params['product_code'] = $this->product_code;
        return $params;
    }

    /**
     * 获取待签名的字符串
     * @param $params
     * @return string
     */
    public function getSignContent($params)
    {
        ksort($params);
        $stringToBeSigned = "";
        $i = 0;
        foreach ($params as $k => $v) {
            if (!empty($v) && "@" != substr($v, 0, 1)) {
                // 转换成目标字符集
                $v = $this->characet($v, $this->charset);

                if ($i == 0) {
                    $stringToBeSigned .= "$k" . "=" . $v;
                } else {
                    $stringToBeSigned .= "&" . "$k" . "=" . $v;
                }
                $i++;
            }
        }

        unset ($k, $v);
        return $stringToBeSigned;
    }

    /**
     * 转换字符集编码
     * @param $data
     * @param $targetCharset
     * @return string
     */
    protected function characet($data, $targetCharset)
    {
        if (strcasecmp($this->file_charset, $targetCharset) != 0) {
            $data = mb_convert_encoding($data, $targetCharset, $this->file_charset);
        }

        return $data;
    }

    /**
     * 签名
     * @param $data
     * @param string $signType
     * @return string
     */
    protected function sign($data, $signType = "RSA")
    {
        $res = "-----BEGIN RSA PRIVATE KEY-----\n" .
            wordwrap($this->private_key, 64, "\n", true) .
            "\n-----END RSA PRIVATE KEY-----";

        if ("RSA2" == $signType) {
            openssl_sign($data, $sign, $res, OPENSSL_ALGO_SHA256);
        } else {
            openssl_sign($data, $sign, $res);
        }

        $sign = base64_encode($sign);

        return $sign;
    }

    protected function getNotifyUrl()
    {
        return '';
    }

    protected function getReturnUrl()
    {
        return '';
    }

    /**
     * @param $para_temp
     * @return string
     */
    protected function buildRequestForm($para_temp) {

        $sHtml = "<form id='alipaysubmit' name='alipaysubmit' action='".$this->gateway_url."?charset=".trim($this->post_charset)."' method='POST'>";
        while (list ($key, $val) = each ($para_temp)) {
            if (!empty($val)) {
                $val = str_replace("'","&apos;",$val);
                $sHtml.= "<input type='hidden' name='".$key."' value='".$val."'/>";
            }
        }
        $sHtml = $sHtml."<input type='submit' value='ok' style='display:none;''></form>";
        $sHtml = $sHtml."<script>document.forms['alipaysubmit'].submit();</script>";

        return $sHtml;
    }
}
