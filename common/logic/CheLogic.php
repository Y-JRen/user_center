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
}