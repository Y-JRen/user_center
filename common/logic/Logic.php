<?php

namespace common\logic;


use yii\base\Object;

/**
 * 基础逻辑类
 * Class Logic
 * @package common\server
 */
abstract class Logic extends Object
{
    /**
     * 错误码
     * @var int
     */
    public $errorCode = 0;

    /**
     * 错误信息
     * @var null
     */
    public $error = null;

    /**
     * 新增错误信息
     * @param $error
     */
    public function setError($error)
    {
        $this->error = $error;
    }

    /**
     * 获取最后一条错误信息
     * @return mixed
     */
    public function getError()
    {
        return $this->error;
    }

    /**
     * 设置错误码
     * @param $errorCode
     */
    public function setErrorCode($errorCode)
    {
        $this->errorCode = $errorCode;
    }

    /**
     * 获取错误码
     * @return int
     */
    public function getErrorCode()
    {
        return $this->errorCode;
    }

    /**
     * 创建对象
     *
     * @param array $config
     * @param bool $flush
     * @return static
     */
    public static function instance($config = [],$flush = false)
    {
        return Instance::instance(get_called_class(), $config, $flush);
    }

    /**
     * 销毁对象
     */
    public static function destructInstance()
    {
        Instance::destructInstance(get_called_class());
    }

}

/**
 * Class Instance
 *
 * @package common\server
 */
class Instance
{
    static $_instance = [];

    /**
     * 创建实例
     * @param string $name
     * @param array $config
     * @param bool $flush
     * @return mixed
     */
    public static function instance($name, $config, $flush)
    {
        if ($flush || !isset(self::$_instance[$name])) {
            self::$_instance[$name] = new $name($config);
        }
        return self::$_instance[$name];
    }

    /**
     * 销毁实例
     * @param $name
     */
    public static function destructInstance($name)
    {
        if (isset(self::$_instance[$name])) {
            unset(self::$_instance[$name]);
        }
    }
}