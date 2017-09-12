<?php
/**
 * Created by PhpStorm.
 * User: legend
 * Date: 2017/9/12
 * Time: 下午6:26
 */

namespace passport\modules\inside\controllers;



use passport\modules\inside\models\CarHousekeeper;

class CarController extends BaseController
{
    /**
     * 汽车智能硬件接口
     */
    public function actionHardware($uid)
    {
        $car = CarHousekeeper::find()->where(['uid' => $uid])->orderBy('id asc')->one();
        return $this->_return($car);
    }
}