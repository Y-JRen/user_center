<?php

namespace passport\helpers;

use Yii;
use yii\helpers\ArrayHelper;


/**
 * 相关项目配置
 * Class Config
 * @package passport\helpers
 */
class Config
{
    public static $orderHtmlExpire = 3600;// 订单的html保存时间

    public static $tokenExpire = 864000;//user_token保存时间

    /**
     * 获取当前请求的所有参数|或其中某一个参数
     *
     * @param $key
     * @return array
     */
    public static function getRequestAllParams($key = null)
    {
        $get = Yii::$app->request->get();
        $post = Yii::$app->request->post();
        $params = array_merge($get, $post);
        return is_null($key) ? $params : ArrayHelper::getValue($params, $key);
    }

    /**
     * 获取各平台的相关配置
     * @param null $domain
     * @return mixed
     */
    public static function params($domain = null)
    {
        $projects = ArrayHelper::getValue(Yii::$app->params, 'projects');
        if (empty($domain)) {
            return $projects;
        } else {
            return ArrayHelper::getValue($projects, $domain);
        }
    }

    /**
     * 获取订单的回调地址
     * @param $platformId
     * @return bool|mixed
     */
    public static function getOrderCallbackUrl($platformId)
    {
        $domain = ArrayHelper::getValue(self::getPlatformArray(), $platformId);
        $projects = ArrayHelper::getValue(Yii::$app->params, 'projects');
        if ($projects) {
            $domainInfo = ArrayHelper::getValue($projects, $domain);
            return ArrayHelper::getValue($domainInfo, 'orderCallbackUrl');
        }
        return false;
    }

    /**
     * 获取平台ID
     * @return int
     */
    public static function getPlatform()
    {
        $domain = self::getRequestAllParams('domain');
        $platformArray = array_flip(self::getPlatformArray());
        return ArrayHelper::getValue($platformArray, $domain, 0);
    }

    /**
     * 平台对应
     * @return array
     */
    public static function getPlatformArray()
    {
        return [
            1 => 'che.com',
            2 => 'crm',
            3 => 'erp',
        ];
    }

    /**
     * 生成订单ID
     *
     * @return string
     */
    public static function createOrderId()
    {
        return date('YmdHis') . static::getPlatform() . rand(100, 999);
    }

    /**
     * 获取微信支付配置
     * @return array
     */
    public static function getWeChatConfig()
    {
        return Yii::$app->params['wechat']['che.com'];
    }

    /**
     * 获取客户端类型
     * ios pc
     * @return string
     */
    public static function getClientType()
    {
        return self::getRequestAllParams('client_type');
    }

    /**
     * 获取支付宝相关配置
     * @return array
     */
    public static function getAlipayConfig()
    {
        return [
            'appid' => '2016090501850905',
            'private_key' => 'MIIEvgIBADANBgkqhkiG9w0BAQEFAASCBKgwggSkAgEAAoIBAQCrirPg3gjxKkxG6OvtRhkUVmyvCkKEeMfUxfMwuY91eOPVgoxgxaSGuC1G+Mg5pRjne42g2R5XYvx0NWu/t/eQbXDBV87fCbz46WU6+XJXaHGuyElJkU/PIynvsOt/bhEaTivQ/kB34IMjgOKPS1Pl8HOIWd3v9qJDuF0mRlA8uwUJv5LSoH5A8+Rw9OsYOtduVktj4Ut3fF7JT0FzOjRUe2S+POEkmidCY71qTP4eFa/EIg8GuR8i7UKLJfUDWE8VlOe2SReUg7/AvlKxuvY36rZTJvq+CQ+ygeoFZHKEmVAl4pZcAJ0Z0uUE4cVf+GX84pdVn7iEfE8+gvCOwjj1AgMBAAECggEAAVe/mrYCOekL2c4+8oeG8LrQdPpOlPzhC5BVG/+H2PKOTgCMmsFRTWCpshGGd7UMIdu8uxKYAzUaJsq4QjLtdCr3I/xM+T/5Q7WH/lMvivGdWjHlKPdDOarbzC2FWmUBK0SLYUJsPMP8Is4Cd/6t9HHSZbHWY+d5U7BgwkjwndkRnNs5S1LLmNTaZx5rQJ0Pcr9wFOXFFMBY3/qKSMWvUBn+f9IS/IGD6Q5mEEIVGaTfsjGF9voHGIkEdmBow5bcVetUG3nTxeqhzLt2aaIIajsfQ6PPSs1zV7icW4m0zUAYN4ZrR4ROIeVZM9v3lFjIVz4DdomVxSGudOiJ17is4QKBgQDvEdT2GinjgrtewsBD+BcxkZ7ZkjqVCkXqdAvmHqeQk/to5HrMNImrp10ShyQdhj8Hbfqi+s4OMR64E2rrS1bPWj5/Rc3PXMQM7z52Cm4fTNkPwtijOL2Q7EL2N91eNjLwjyrCar/0BxJlKXDnkPP7JxEgnet1AfnJv8iOZh0H7QKBgQC3sKMtKjJHF9Ia5y76clKGhPHqam1V2sja+Dy8+8MArWI90H1QCEQseW/m8Q8/i2A4BbORuRVyePxz0J8FujBx3m/YUZMcbnB+vOiVvogzGzTVl0Fmr1dmPInEUjyOFw9PT4oD4X23cdNpy+Lsn23eGQKD6pSr8pCzHGwpI+VEKQKBgQDG6Q9HtH0lzqAXN/LAmqqxk3eXjxMu7l09tVAxJ1BD+twzaebCAPSK3bWStN5Xslq+08K11/eZ10S343ASVZzk6TEWs/2JiqWUHXrau9LkOAxiELwvEganjewo8FI/ENlRTc1F9YvAfiHQCarnALP/Q4H8oWhG8l3ifq9fy5ZcAQKBgQCfi9m/UeZHn6YLXf+M4p1RX8mrfPMoXhaVMoW288mlHfO1kxA04mksY/HRGlHPNKTHJBSbHrJFMla71Vk6JUUMslJdBkWLrVUij7xIzCwKj7ftSSoyIVv9XdmfXnpgRCE1FvXedw21tzUUsFWShstr3Dc2Exs82jvahnZfqWDqOQKBgHJQgVSU60IefcWJdtamytagEYlncJoF7Fv2vcjoSUdiI0WItvDBNCPqcUBpUzOqpvZwWEF4vtOBLXc4bWu9e59X+QRGpCPuc+DO1ALjXSVtfS009SPf20bLvpWo/Q79Zq3chw1dx9WsRC4owiqkdP5e0JIEmMwERwF/oQemu76o',
            'alipay_public_key' => 'MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAq4qz4N4I8SpMRujr7UYZFFZsrwpChHjH1MXzMLmPdXjj1YKMYMWkhrgtRvjIOaUY53uNoNkeV2L8dDVrv7f3kG1wwVfO3wm8+OllOvlyV2hxrshJSZFPzyMp77Drf24RGk4r0P5Ad+CDI4Dij0tT5fBziFnd7/aiQ7hdJkZQPLsFCb+S0qB+QPPkcPTrGDrXblZLY+FLd3xeyU9Bczo0VHtkvjzhJJonQmO9akz+HhWvxCIPBrkfIu1CiyX1A1hPFZTntkkXlIO/wL5Ssbr2N+q2Uyb6vgkPsoHqBWRyhJlQJeKWXACdGdLlBOHFX/hl/OKXVZ+4hHxPPoLwjsI49QIDAQAB'
        ];
    }
}