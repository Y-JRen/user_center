<?php
/**
 * Created by PhpStorm.
 * User: legend
 * Date: 2017/9/19
 * Time: 下午6:06
 */

namespace backend\controllers;


use common\logic\CheLogic;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\web\Controller;

class CarController extends Controller
{
    /**
     * 获取厂商
     * @param $brandId
     * @return string
     */
    public function actionFactory($brandId)
    {
        $result = CheLogic::instance()->factory($brandId);
        $html = '';
        foreach ($result as $key => $val) {
            $html .= "<option value='{$key}'>{$val}</option>";
        }
        return $html;
    }

    /**
     * 获取车系
     * @param $brandId
     * @param $factoryId
     * @return string
     */
    public function actionSeries($brandId, $factoryId)
    {
        $result = CheLogic::instance()->series($brandId, $factoryId);
        $html = '';
        foreach ($result as $key => $val) {
            $html .= "<option value='{$key}'>{$val}</option>";
        }
        return $html;
    }

    /**
     * 获取车型
     * @param $seriesId
     * @return string
     */
    public function actionModel($seriesId)
    {
        $result = CheLogic::instance()->model($seriesId);
        $html = '';
        foreach ($result as $key => $val) {
            $html .= "<option value='{$key}'>{$val}</option>";
        }
        return $html;
    }
}