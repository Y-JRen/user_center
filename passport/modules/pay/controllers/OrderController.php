<?php
/**
 * Created by PhpStorm.
 * User: xiongjun
 * Date: 2017/6/5
 * Time: 16:36
 */

namespace passport\modules\pay\controllers;


use passport\controllers\BaseController;
use passport\modules\pay\forms\OrderForm;
use passport\modules\pay\logic\PayLogic;

/**
 * 下单订单
 *
 * Class OrderController
 *
 * @package passport\modules\pay\controllers
 */
class OrderController extends BaseController
{
    /**
     * 生成订单
     *
     */
    public function actionIndex()
    {
        $param = \Yii::$app->request->post();
        $data['OrderForm'] = $param;
        $data['OrderForm']['status'] = 1;
        $model = new OrderForm();
        if($model->load($data) && $model->save()){
            $rest = PayLogic::instance()->pay($model);

            return $this->_return($rest);
        } else {
            return $this->_error(400, $model->errors);
        }
    }
}