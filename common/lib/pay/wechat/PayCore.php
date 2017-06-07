<?php
/**
 * Created by PhpStorm.
 * User: xiongjun
 * Date: 2017/6/5
 * Time: 14:12
 */

namespace common\lib\pay\wechat;


use common\logic\ApiLogsLogic;
use common\logic\Logic;
use passport\helpers\Config;
use Yii;

/**
 * 微信支付
 * Class PayCore
 * @package common\lib\wechat\pay
 */
class PayCore extends Logic
{
    /**
     * 企业付款
     */
    const PAY_URL = 'https://api.mch.weixin.qq.com/mmpaymkttransfers/promotion/transfers';

    /**
     * 查询企业付款
     */
    const PAY_INFO_URL = 'https://api.mch.weixin.qq.com/mmpaymkttransfers/gettransferinfo';

    /**
     * 统一下单
     */
    const UNIFIED_ORDER = 'https://api.mch.weixin.qq.com/pay/unifiedorder';

    /**
     * 查询订单
     */
    const ORDER_QUERY = 'https://api.mch.weixin.qq.com/pay/orderquery';

    /**
     * 关闭订单
     */
    const CLOSE_ORDER ='https://api.mch.weixin.qq.com/pay/closeorder';

    const SHORT_URL = 'https://api.mch.weixin.qq.com/tools/shorturl';


    public $weChatConfig;

    public function init()
    {
        $this->weChatConfig = Config::getWeChatConfig();
    }


    /**
     * 统一下单
     *
     * @param array $data
     * @return bool|mixed
     */
    public function unifiedOrder($data)
    {
        $data['appid'] = $this->weChatConfig['appid'];
        $data['mch_id'] = $this->weChatConfig['mch_id'];
        $data['device_info'] = 'web';
        $data['nonce_str'] = $this->nonceStr();
        $data['sign'] = $this->sign($data);
        $dataXml = $this->buildXml($data);
        return $this->http(static::UNIFIED_ORDER , $dataXml);
    }

    /**
     * 转换短链接
     * @param $longUrl
     * @return bool|mixed
     */
    public function shortUrl($longUrl)
    {
        $data['long_url'] = $longUrl;
        $weChatConfig = Config::getWeChatConfig();
        $data['appid'] = $weChatConfig['appid'];
        $data['mch_id'] = $weChatConfig['mch_id'];
        $data['nonce_str'] = $this->nonceStr();
        $data['sign'] = $this->sign($data);
        $dataXml = $this->buildXml($data);
        return $this->http(static::UNIFIED_ORDER , $dataXml);
    }

    /**
     * 数组转XML
     * @param array $arr
     * @return string
     */
    public function buildXml(array $arr)
    {
        $xml = "<xml>";
        foreach ($arr as $key => $val) {
            if (is_numeric($val)) {
                $xml .= "<" . $key . ">" . $val . "</" . $key . ">";
            } else
                $xml .= "<" . $key . "><![CDATA[" . $val . "]]></" . $key . ">";
        }
        $xml .= "</xml>";
        return $xml;
    }

    /**
     * Http基础库 使用该库请求微信服务器
     * @param $url
     * @param string $xml
     * @param integer $second
     * @param boolean $check 是否需要验证证书
     * @return bool|mixed
     */
    protected function http($url, $xml, $second = 30, $check = false)
    {
        //初始化curl
        $ch = curl_init();
        //设置超时
        curl_setopt($ch, CURLOPT_TIMEOUT, $second);
        //这里设置代理，如果有的话
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt($ch, CURLOPT_HEADER, FALSE);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_POST, TRUE);

        if($check) {
            curl_setopt($ch, CURLOPT_SSLKEYTYPE, 'PEM');
            curl_setopt($ch, CURLOPT_SSLCERT, \Yii::$app->basePath . '/../apiclient_cert.pem');
            curl_setopt($ch, CURLOPT_SSLKEY, \Yii::$app->basePath . '/../apiclient_key.pem');
        }
        curl_setopt($ch, CURLOPT_POSTFIELDS, $xml);
        $data = curl_exec($ch);
        if ($data) {
            curl_close($ch);
            return $this->xmlToArray($data);
        } else {
            $error = curl_errno($ch);
            echo "curl出错，错误码:$error" . "<br>";
            echo "<a href='http://curl.haxx.se/libcurl/c/libcurl-errors.html'>错误原因查询</a></br>";
            curl_close($ch);
            return false;
        }
    }

    /**
     * 生成随机字符串
     * @param int $length
     * @return string
     */
    public function nonceStr($length = 16)
    {
        $chars = array('a', 'b', 'c', 'd', 'e', 'f', 'g', 'h',
            'i', 'j', 'k', 'l', 'm', 'n', 'o', 'p', 'q', 'r', 's',
            't', 'u', 'v', 'w', 'x', 'y', 'z', 'A', 'B', 'C', 'D',
            'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O',
            'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z',
            '0', '1', '2', '3', '4', '5', '6', '7', '8', '9');
        $keys = array_rand($chars, $length);
        $nonceStr = '';
        for ($i = 0; $i < $length; $i++) {
            $nonceStr .= $chars[$keys[$i]];
        }
        return $nonceStr;
    }

    /**
     * 签名
     * @param array $nonceArr
     * @return string
     */
    public function sign($nonceArr)
    {
        if (empty($nonceArr)) {
            return false;
        }
        ksort($nonceArr);
        $buff = '';
        foreach ($nonceArr as $k => $v) {
            $buff .= $k . "=" . $v . "&";
        }
        if (strlen($buff) > 0) {
            $buff = substr($buff, 0, strlen($buff) - 1);
        }
        $stringSignTemp = $buff . "&key=" . $this->weChatConfig['pay_key'];
        return strtoupper(md5($stringSignTemp));
    }

    /**
     * xml转数组
     * @param $xml
     * @return mixed
     */
    function xmlToArray($xml)
    {
        //禁止引用外部xml实体
        libxml_disable_entity_loader(true);
        $values = json_decode(json_encode(simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA)), true);
        $filePath = \Yii::$app->getRuntimePath() . DIRECTORY_SEPARATOR . 'logs' . DIRECTORY_SEPARATOR . 'wechat' .
            DIRECTORY_SEPARATOR . date("Y-m") . DIRECTORY_SEPARATOR . 'wechat_pay_'.date("Y-m-d").'.data';
        //写入日志
        ApiLogsLogic::instance()->addLogging($filePath, $values);
        return $values;
    }
}