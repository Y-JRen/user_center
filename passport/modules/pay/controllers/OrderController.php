<?php
/**
 * Created by PhpStorm.
 * User: xiongjun
 * Date: 2017/6/5
 * Time: 16:36
 */

namespace passport\modules\pay\controllers;


use Yii;
use passport\controllers\BaseController;
use passport\modules\pay\models\OrderForm;
use passport\modules\pay\logic\PayLogic;
use yii\helpers\ArrayHelper;

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
     *
     * 充值订单
     * @return array
     */
    public function actionIndex()
    {
        $param = \Yii::$app->request->post();
        $data['OrderForm'] = $param;
        // 验证openid
        if ($param['order_type'] == 1 && $param['order_subtype'] == 'wechat_jsapi') {
            if (empty($param['openid'])) {
                return $this->_error(2006);
            } else {
                $data['OrderForm']['remark'] = json_encode(['openid' => $param['openid']]);
            }
        }

        $model = new OrderForm();
        if ($model->load($data) && $model->save()) {
            //充值
            if ($model->order_type == 1) {
                $result = PayLogic::instance()->pay($model);
                $status = ArrayHelper::getValue($result, 'status', 0);
                $data = ArrayHelper::getValue($result, 'data');
                if ($status == 0) {
                    $data['platform_order_id'] = $model->platform_order_id;
                    $data['order_id'] = $model->order_id;
                    $data['notice_platform_param'] = $model->notice_platform_param;
                }
                return $this->_return($data, $status);
            } else {
                return $this->_error(2005);
            }
        } else {
            return $this->_error(2001, $model->errors);
        }
    }

    /**
     * 充值订单、快捷支付订单
     *
     * 快捷支付是充值后立即消费，消费必须异步回调方法中处理
     */
    public function actionRecharge()
    {
        $param = Yii::$app->request->post();
        if ($param['order_type'] != OrderForm::TYPE_RECHARGE) {
            return $this->_error(2007);
        }

        $model = new OrderForm();
        if ($model->load($param, '') && $model->save()) {// 创建充值订单
            $result = PayLogic::instance()->pay($model);

            $status = ArrayHelper::getValue($result, 'status', 0);
            $data = ArrayHelper::getValue($result, 'data');
            if ($status == 0) {
                $data['platform_order_id'] = $model->platform_order_id;
                $data['order_id'] = $model->order_id;
                $data['notice_platform_param'] = $model->notice_platform_param;
            }
            return $this->_return($data, $status);
        } else {
            return $this->_error(2001, $model->errors);
        }
    }


    /**
     * 消费订单
     */
    public function actionConsume()
    {
        $param = Yii::$app->request->post();
        if ($param['order_type'] != OrderForm::TYPE_CONSUME) {
            return $this->_error(2007);
        }
        $model = new OrderForm();
        if ($model->load($param, '') && $model->save()) {
            $model->consumeSave();

            $data['platform_order_id'] = $model->platform_order_id;
            $data['order_id'] = $model->order_id;
            $data['notice_platform_param'] = $model->notice_platform_param;
            return $this->_return($data);
        } else {
            return $this->_error(2101, $model->errors);
        }
    }

    /**
     * 退款订单
     *
     * 退款时，只会生成一个退款订单
     * 财务同意后，用户余额增加
     * 有点类似线下充值
     *
     * @return array
     */
    public function actionRefund()
    {
        $param = Yii::$app->request->post();
        if ($param['order_type'] != OrderForm::TYPE_REFUND) {
            return $this->_error(2007);
        }
        $model = new OrderForm();
        if ($model->load($param, '') && $model->save()) {
            $data['platform_order_id'] = $model->platform_order_id;
            $data['order_id'] = $model->order_id;
            $data['notice_platform_param'] = $model->notice_platform_param;

            return $this->_return($data);
        } else {
            return $this->_error(2201, $model->errors);
        }
    }

    /**
     * 提现
     */
    public function actionCash()
    {
        $param = Yii::$app->request->post();
        if ($param['order_type'] != OrderForm::TYPE_CASH) {
            return $this->_error(2007);
        }
        $model = new OrderForm();
        if ($model->load($param, '') && $model->save()) {
            $model->cashSave();

            $data['platform_order_id'] = $model->platform_order_id;
            $data['order_id'] = $model->order_id;
            $data['notice_platform_param'] = $model->notice_platform_param;
            return $this->_return($data);
        } else {
            return $this->_error(2301, $model->errors);
        }
    }
}