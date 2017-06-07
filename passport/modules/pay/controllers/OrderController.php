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
use yii\helpers\ArrayHelper;
use yii\helpers\Url;

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
     * 充值订单
     *
     */
    public function actionIndex()
    {
        $param = \Yii::$app->request->post();
        $data['OrderForm'] = $param;
        //初始化订单状态默认位 1
        $data['OrderForm']['status'] = 1;
        $model = new OrderForm();
        if ($model->load($data) && $model->save()) {
            if ($model->order_type == 1) {
                $result = PayLogic::instance()->pay($model);
                if (is_array($result)) {
                    return $this->_return(ArrayHelper::getValue($result, 'status', 0), ArrayHelper::getValue($result, 'data'));
                } else {
                    return $this->_return($result);
                }

            }

        } else {
            return $this->_error(2001, $model->errors);
        }
    }
}