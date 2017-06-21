<?php
/**
 * Created by PhpStorm.
 * User: huawei
 * Date: 2017/6/20
 * Time: 11:31
 */

namespace backend\controllers;

use common\logic\FinanceLogic;
use common\logic\HttpLogic;
use yii\helpers\ArrayHelper;
use yii\web\Controller;

/**
 * 财务系统相关接口
 * Class FinanceController
 * @package backend\controllers
 */
class FinanceController extends Controller
{
    /**
     * 获取该组织架构的卡号账户
     */
    public function actionGetAccounts($orgId)
    {
        return FinanceLogic::instance()->getAccounts($orgId);
    }

    /**
     * 获取收款类型
     */
    public function actionGetTag($parentId = null)
    {
        return FinanceLogic::instance()->getTagByParent($parentId);
    }

    /**
     * 推送账单流水到财务系统
     */
    public function actionPayment()
    {

    }

    public function getUrl($path, $data = [])
    {
        $data['_token'] = 'debf6cc22a8baf00904acc5f42535575';
        return 'http://test.pocket.checheng.net/api/' . $path . '?' . http_build_query($data);
    }
}