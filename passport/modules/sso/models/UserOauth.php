<?php

namespace passport\modules\sso\models;


use common\models\UserOauth;
use yii\helpers\ArrayHelper;

class UserOauth extends \common\models\UserOauth
{
    // 登录类型设置
    const LOGIN_TYPE_QQ = 1;//QQ
    const LOGIN_TYPE_WX = 2;//微信
    const LOGIN_TYPE_WB = 3;//微博

    public static $loginArray = [
        self::LOGIN_TYPE_QQ => 'QQ',
        self::LOGIN_TYPE_WX => '微信',
        self::LOGIN_TYPE_WB => '微博',
    ];

    /**
     * 获取第三方登录类型
     * @param $key
     * @return mixed|string
     */
    public static function getLoginType($key)
    {
        $arr = self::$loginArray;
        if (ArrayHelper::getValue($arr, $key)) {
            return ArrayHelper::getValue($arr, $key);
        } else {
            return '登录方式不被允许';
        }
    }

}