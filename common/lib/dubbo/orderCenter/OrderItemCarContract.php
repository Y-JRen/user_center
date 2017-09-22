<?php
/**
 * Created by PhpStorm.
 * User: 雕
 * Date: 2017/8/28
 * Time: 16:24
 */

namespace common\lib\dubbo\orderCenter;

use dubbo\CheDubbo;

class OrderItemCarContract  extends DubboOrderCenterBase
{
    private $uriPath    = 'orderItemCarContract';
    private $service    = 'com.che.commonService.orderCenter.contract.OrderItemCarContract';

    public function __construct()
    {
        parent::__construct();
        $registryAddress    = $this->zookeeperAddress;
        $applicationName    = $this->dubboPubicParam['appName'];
        $version            = $this->version;
        $group              = $this->group;
        try {
            $this->cheDubbo = new CheDubbo($registryAddress, $applicationName, $version, $group);
        } catch (\Exception $e) {
            //
        }
    }

    public function queryCarByOrderId($orderId)
    {
        $params    = array_merge($this->dubboPubicParam, ['param' => intval($orderId)]);
        return $this->callDubboAndFormatResult($this->service, $this->uriPath, __FUNCTION__, $params);
    }


    public function updateCar(array $update)
    {
        $params    = array_merge($this->dubboPubicParam, ['param' => $update]);
        return $this->callDubboAndFormatResult($this->service, $this->uriPath, __FUNCTION__, $params);
    }

}