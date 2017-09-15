<?php
/**
 * Created by PhpStorm.
 * User: 雕
 * Date: 2017/8/23
 * Time: 18:16
 */
namespace common\lib\dubbo;

class DubboBase
{
    protected $dubboPubicParam = [];

    public function __construct()
    {
        $this->dubboPubicParam = [
            'productName' => PROD_NAME,
            'productCode' => PROD_CODE,
            'appName' => APP_NAME,
            'appCode' => APP_ID,
            'ip' => $this->getServerIp(),
        ];
    }



    private function getServerIp()
    {
        if (isset($_SERVER)) {
            if ($_SERVER['SERVER_ADDR']) {
                $serverIp = $_SERVER['SERVER_ADDR'];
            } else {
                $serverIp = $_SERVER['LOCAL_ADDR'];
            }
        } else {
            $serverIp = \getenv('SERVER_ADDR');
        }
        return $serverIp;
    }
}