<?php
/**
 * Created by PhpStorm.
 * User: huawei
 * Date: 2017/6/21
 * Time: 14:31
 */

namespace common\logic;


use Yii;
use yii\helpers\ArrayHelper;

/**
 * 财务系统相关接口
 * Class FinanceLogic
 * @package common\logic
 */
class FinanceLogic extends Logic
{
    /**
     * 获取组织架构并转成一维数组
     */
    public function getOrganization()
    {
        $redis = Yii::$app->redis;
        $key = 'FinanceLogic_getOrganization';
        if ($data = $redis->get($key)) {
            return json_decode($data, true);
        }

        $url = $this->getUrl('organization-tree');
        $result = HttpLogic::instance()->http($url, 'GET');
        $resultArr = json_decode($result, true);
        $data = [];
        if ($resultArr['success']) {
            $this->arrayToOne($resultArr['data'], $data);
        }

        $redis->set($key, json_encode($data));
        $redis->expire($key, 3600);
        return $data;
    }

    /**
     * 获取该组织架构的卡号账户
     */
    public function getAccounts($orgId)
    {
        $redis = Yii::$app->redis;
        $key = 'FinanceLogic_getAccounts_' . $orgId;
        if ($data = $redis->get($key)) {
            return $data;
        }

        $data = ['organization_id' => $orgId];
        $url = $this->getUrl('accounts', $data);
        $data = HttpLogic::instance()->http($url, 'GET');

        $redis->set($key, $data);
        $redis->expire($key, 3600);
        return $data;
    }

    /**
     * 获取所有科目类型
     */
    public function getTag()
    {
        $redis = Yii::$app->redis;
        $key = 'FinanceLogic_actionGetTag';
        if ($data = $redis->get($key)) {
            return json_decode($data, true);
        }

        $url = $this->getUrl('tag-tree');
        $result = HttpLogic::instance()->http($url, 'GET');
        $resultArr = json_decode($result, true);
        $data = [];
        if ($resultArr['success']) {
            $this->arrayToOne($resultArr['data'], $data);
        }

        $redis->set($key, json_encode($data));
        $redis->expire($key, 3600);
        return $data;
    }

    /**
     * 获取指定父id的类型
     * @param null $parentId
     * @return array
     */
    public function getTagByParent($parentId = null)
    {
        $data = $this->getTag();
        $result = [];
        foreach ($data as $key => $value) {
            if (is_null($parentId)) {
                if (empty($value['parent_id'])) {
                    $result[] = $value;
                }
            } else {
                if ($value['parent_id'] == $parentId) {
                    $result[] = $value;
                }
            }
        }

        return empty($result) ? null : json_encode($result);
    }

    /**
     * 推送账单流水到财务系统
     */
    public function payment($data)
    {
        $url = $this->getUrl('payment');
        $resultInfo = HttpLogic::instance()->http($url, 'POST', $data);
        $result = json_decode($resultInfo, true);
        if (empty($result['success'])) {
            Yii::error(json_encode($result['message']));
        }
    }


    /**
     * 多维数组转一维数组
     * @param $array
     * @param $data
     */
    public function arrayToOne($array, &$data)
    {
        foreach ($array as $key => $value) {
            $children = ArrayHelper::getValue($value, 'children');
            unset($value['children']);
            empty($value) ? null : array_push($data, $value);

            if (!empty($children)) {
                $childrenData = $this->arrayToOne($children, $data);
                empty($childrenData) ? null : array_push($data, $childrenData);
            }
        }
    }

    public function getUrl($path, $data = [])
    {
        $data['_token'] = 'debf6cc22a8baf00904acc5f42535575';
        return 'http://test.pocket.checheng.net/api/' . $path . '?' . http_build_query($data);
    }
}