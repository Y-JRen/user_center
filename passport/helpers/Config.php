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
    public static function params($domain = null)
    {
        $params = ArrayHelper::getValue(Yii::$app->params, 'projects');
        if (empty($domain)) {
            return $params;
        } else {
            return ArrayHelper::getValue($params, $domain);
        }

    }

    /**
     * 获取平台ID
     * @return int
     */
    public static function getPlatform()
    {
        return 1;
    }

    /**
     * 生成订单ID
     *
     * @return string
     */
    public static function createOrderId()
    {
        return date('YmdHis').static::getPlatform().rand(100,999);
    }

    public static function getWeChatConfig()
    {
        return Yii::$app->params['wechat']['che.com'];
    }
    
    public static function getClientType()
    {
    	return 'web';
    }
}