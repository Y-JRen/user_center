<?php
/**
 * Created by PhpStorm.
 * User: 雕
 * Date: 2017/8/25
 * Time: 11:06
 */

namespace common\dubbo\orderCenter;

use dubbo\CheDubbo;
use Yii;
use yii\helpers\ArrayHelper;
use common\dubbo\DubboBase;

abstract class DubboOrderCenterBase extends DubboBase
{
    protected $zookeeperAddress = null;
    protected $group = null;
    protected $version = null;
    protected $cheDubbo = null;

    public function __construct()
    {
        parent::__construct();
        $config = ArrayHelper::getValue(Yii::$app->params, 'dubbo.orderCenter');
        if ($config && is_array($config) && isset($config['zookeeper'])) {
            $this->zookeeperAddress = $config['zookeeper'];
            $this->version = ArrayHelper::getValue($config, 'version');
            $this->group = ArrayHelper::getValue($config, 'group');
        } else {
            throw new \Exception('没有配置订单中心的dubbo服务的zookeeper地址');
        }
    }

    protected function callDubboAndFormatResult($service, $uriPath, $method, $params)
    {
        $result = '';
        if ($this->cheDubbo instanceof  CheDubbo) {
            $result = $this->cheDubbo->callServiceMethod($service, $uriPath, $method, $params);
            var_dump($result);
        }
        return $this->formatResult($result);
    }

    protected function formatResult($strResult)
    {
        $arrResult = json_decode($strResult, true);
        if (is_array($arrResult) && $arrResult['success'] == true && $arrResult['resultBodyObject']) {
            //请求成功，且有数据
            return $arrResult['resultBodyObject'];
        } else {
            //请求失败或者请求结果为空
            return false;
        }
    }
}