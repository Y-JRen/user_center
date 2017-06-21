<?php
/**
 * Created by PhpStorm.
 * User: huawei
 * Date: 2017/6/20
 * Time: 14:17
 */

namespace common\logic;

use passport\helpers\Config;
use Yii;
use yii\helpers\ArrayHelper;


/**
 * ERP退款确认接口
 *
 * @package common\logic
 */
class RefundLogin extends Logic
{
    /**
     * 订单确认
     */
    public function orderConfirm($data)
    {
        $data['sign'] = '123';
        $path = '/api/sale/refundCheck';
        $url = $this->getUrl($path);
        $result = HttpLogic::instance()->http($url, 'POST', $data);
        return ArrayHelper::getValue($result, 'statusCode', false);
    }

    /**
     * 金额确认
     */
    public function amountConfirm($data)
    {
        $data['sign'] = '123';
        $path = '/api/sale/loanRefundCheck';
        $url = $this->getUrl($path);
        $result = HttpLogic::instance()->http($url, 'POST', $data);
        return ArrayHelper::getValue($result, 'statusCode', false);
    }

    /**
     * 获取erp的请求地址
     * @param $path
     * @return string
     */
    protected function getUrl($path)
    {
        $conf = Config::params('erp');
        $baseUrl = ArrayHelper::getValue($conf, 'callbackDomain');
        return $baseUrl . $path;
    }
}