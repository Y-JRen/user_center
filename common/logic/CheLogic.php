<?php
/**
 * Created by PhpStorm.
 * User: legend
 * Date: 2017/8/14
 * Time: 上午10:36
 */

namespace common\logic;

use Yii;
use yii\helpers\ArrayHelper;


/**
 * che.com的相关接口
 *
 * Class CheLogic
 * @package common\logic
 */
class CheLogic extends Logic
{
    /**
     * 贷款短信通知
     * @param $orderId
     * @return boolean
     */
    public function loanArrive($orderId)
    {
        $apiUrl = 'api/outer/loanArrive';
        $url = ArrayHelper::getValue(Yii::$app->params, ['projects', 'che.com', 'apiDomain']) . $apiUrl . '?orderNo=' . $orderId;

        $resultInfo = HttpLogic::instance()->http($url, 'GET');
        $result = json_decode($resultInfo, true);
        if (empty(ArrayHelper::getValue((array)$result, 'statusCode'))) {
            Yii::error("{$orderId}通知电商短信失败;url:{$url};返回值：" . var_export($result, true), 'loanArrive');
        }

        return true;
    }

    /**
     * 获取品牌
     * @return array
     */
    public function brand()
    {
        $redis = Yii::$app->redis;
        $key = 'CheLoginBrand';
        if ($data = $redis->get($key)) {
            return json_decode($data, true);
        }

        $apiUrl = 'che/oa/brands';
        $url = ArrayHelper::getValue(Yii::$app->params, ['projects', 'che.com', 'apiDomain']) . $apiUrl;
        $resultInfo = HttpLogic::instance()->http($url, 'GET');
        $result = json_decode($resultInfo, true);
        if (ArrayHelper::getValue((array)$result, 'code', 0) == 200) {
            $data = ['请选择品牌'] + ArrayHelper::map($result['detail'], 'id', 'name');
            $redis->set($key, json_encode($data));
            $redis->expire($key, 3600);
            return $data;
        } else {
            Yii::error('获取品牌失败');
            return [];
        }
    }

    /**
     * 获取厂商
     * @param $brandId
     * @return array
     */
    public function factory($brandId)
    {
        $apiUrl = 'che/oa/factory';
        $url = ArrayHelper::getValue(Yii::$app->params, ['projects', 'che.com', 'apiDomain']) . $apiUrl . '?brandId=' . $brandId;
        $resultInfo = HttpLogic::instance()->http($url, 'GET');
        $result = json_decode($resultInfo, true);
        if (ArrayHelper::getValue((array)$result, 'code', 0) == 200) {
            return ['请选择厂商'] + ArrayHelper::map($result['detail'], 'id', 'name');
        } else {
            Yii::error("获取[{$brandId}]厂商失败");
            return [];
        }
    }

    /**
     * 获取车系
     * @param $brandId
     * @param $factoryId
     * @return array
     */
    public function series($brandId, $factoryId)
    {
        $apiUrl = 'che/oa/series';
        $url = ArrayHelper::getValue(Yii::$app->params, ['projects', 'che.com', 'apiDomain']) . $apiUrl . '?brandId=' . $brandId . '&factoryId=' . $factoryId;
        $resultInfo = HttpLogic::instance()->http($url, 'GET');
        $result = json_decode($resultInfo, true);
        if (ArrayHelper::getValue((array)$result, 'code', 0) == 200) {
            return ['请选择车系'] + ArrayHelper::map($result['detail'], 'id', 'name');
        } else {
            Yii::error("获取[{$brandId}][{$factoryId}]车系失败");
            return [];
        }
    }

    /**
     * 获取车型
     * @param $seriesId
     * @return array
     */
    public function model($seriesId)
    {
        $apiUrl = 'che/oa/cars';
        $url = ArrayHelper::getValue(Yii::$app->params, ['projects', 'che.com', 'apiDomain']) . $apiUrl . '?seriesId=' . $seriesId;
        $resultInfo = HttpLogic::instance()->http($url, 'GET');
        $result = json_decode($resultInfo, true);
        if (ArrayHelper::getValue((array)$result, 'code', 0) == 200) {
            return ['请选择车型'] + ArrayHelper::map($result['detail'], 'id', 'name');
        } else {
            Yii::error("获取[{$seriesId}]车型失败");
            return [];
        }
    }
}