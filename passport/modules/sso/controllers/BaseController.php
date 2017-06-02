<?php
/**
 * Created by PhpStorm.
 * User: xiongjun
 * Date: 2017/6/2
 * Time: 17:51
 */

namespace passport\modules\sso\controllers;


use yii\rest\Controller;

class BaseController extends Controller
{
    /**
     * 错误码定义
     * @var array
     */
    public static $errorStatuses = [

    ];

    /**
     * 统一返回格式
     *
     * @param string|array|object $data 返回内容
     * @param string $message
     * @param int $errCode
     * @return array
     */
    private function _return($data, $errCode = 0, $message = 'success')
    {
        $message = (!$message) ? static::$errorStatuses[$errCode] : $message;
        return compact('data', 'message', 'code');
    }

    /**
     * 错误信息返回
     * @param int $code
     * @param string|array|object $data
     * @return array
     */
    public function _error($code, $data = null)
    {
        return $this->_return($data, $code);
    }
}