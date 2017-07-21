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

    public static $platformArray = [
        1 => '电商',
        2 => 'CRM',
        3 => 'ERP'
    ];

    /**
     * 生成订单ID
     * @todo inside/trade/search 会用到单号的位数
     *
     * @return string
     */
    public static function createOrderId()
    {
        return 'U' . date('YmdHis') . static::getPlatform() . rand(100, 999);
    }

    /**
     * 获取微信支付配置
     * @return array
     */
    public static function getWeChatConfig()
    {
        return Yii::$app->params['pay']['wechat'];
    }

    /**
     * 获取客户端类型
     * ios pc
     * @return string
     */
    public static function getClientType()
    {
        return strtolower(self::getRequestAllParams('client_type'));
    }

    /**
     * 获取支付宝相关配置
     * @return array
     */
    public static function getAlipayConfig()
    {
        return Yii::$app->params['pay']['alipay_new'];
    }

    public static function getAlipayMobileConfig()
    {
        return Yii::$app->params['pay']['alipay_old'];
    }

    /**
     * 获取支付宝的相关配置
     * @param string $type
     * @return string
     */
    public static function getAliConfig($type = 'default')
    {
        switch ($type) {
            case 'tmall':
                $config = Yii::$app->params['pay']['alipay_tmall'];
                break;
            case 'mobile':
                $config = self::getAlipayMobileConfig();
                break;
            default:
                $config = self::getAlipayConfig();
        }
        return $config;
    }
}