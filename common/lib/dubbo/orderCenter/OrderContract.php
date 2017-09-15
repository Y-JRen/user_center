<?php
/**
 * Created by PhpStorm.
 * User: 雕
 * Date: 2017/8/23
 * Time: 18:14
 */
namespace common\lib\dubbo\orderCenter;

use dubbo\CheDubbo;


class OrderContract extends DubboOrderCenterBase
{
    private $uriPath    = 'orderContract';
    private $service    = 'com.che.commonService.orderCenter.contract.OrderContract';

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

    /**
     * 查询单个订单的详情
     * @param $id
     * @return bool|mixed
     */
    public function queryById($id)
    {
        $params    = array_merge($this->dubboPubicParam, ['param' => intval($id)]);
        return $this->callDubboAndFormatResult($this->service, $this->uriPath, __FUNCTION__, $params);
    }

    /**
     * 批量查询订单详情
     * @param array $idlist
     * @return bool|mixed
     */
    public function queryByIdList(array $idlist)
    {
        $params    = array_merge($this->dubboPubicParam, ['param' => $idlist]);
        return $this->callDubboAndFormatResult($this->service, $this->uriPath, __FUNCTION__, $params);
    }


    public function updateById(array $update)
    {
        $params    = array_merge($this->dubboPubicParam, ['param' => $update]);
        return $this->callDubboAndFormatResult($this->service, $this->uriPath, __FUNCTION__, $params);
    }

}