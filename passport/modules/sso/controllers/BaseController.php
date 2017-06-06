<?php
/**
 * Created by PhpStorm.
 * User: xiongjun
 * Date: 2017/6/2
 * Time: 17:51
 */

namespace passport\modules\sso\controllers;

class BaseController extends \passport\controllers\BaseController
{
    /**
     * SSO 错误码定义
     * @var array
     */
    public static $errorStatuses = [
        
        997 => '发送失败',
        998 => '发送次数超过限制',
        999 => '手机号不正确',
        1001 => '参数错误',
        1002 => '注册失败',
        1003 => '帐号密码不正确',
        1004 => '登入失败',
    ];
}