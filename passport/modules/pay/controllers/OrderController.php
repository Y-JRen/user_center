<?php
/**
 * Created by PhpStorm.
 * User: xiongjun
 * Date: 2017/6/5
 * Time: 16:36
 */

namespace passport\modules\pay\controllers;


use common\helpers\JsonHelper;
use passport\controllers\AuthController;
use passport\modules\pay\models\OrderClose;
use passport\modules\sso\models\UserInfo;
use Yii;
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
class OrderController extends AuthController
{
    public function verbs()
    {
        return [
            'recharge' => ['POST'],
            'consume' => ['POST'],
            'refund' => ['POST'],
            'cash' => ['POST'],
        ];
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
        $param['notice_status'] = 1;
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
        $param['notice_status'] = 1;
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
        $param['notice_status'] = 4;
        if ($model->load($param, '') && $model->save()) {
            $model->refundSave();
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

        // 检测是否有资格提现，实名认证，提现到本人银行卡
        $remark = ArrayHelper::getValue($param, 'remark', '');
        $data = JsonHelper::BankHelper($remark);
        $username = ArrayHelper::getValue($data, 'accountName');
        $verify = $this->cashVerify(Yii::$app->user->id, $username);
        if (!$verify['status']) {
            return $this->_error(2301, $verify['info']);
        }

        $model = new OrderForm();
        $param['notice_status'] = 4;
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

    /**
     * 订单关闭接口
     * 目前只针对充值待处理状态订单
     */
    public function actionClose()
    {
        $orderId = Yii::$app->request->get('orderId');
        /* @var $order OrderClose */
        $order = OrderClose::find()->where(['order_id' => $orderId])->one();
        if ($order) {
            if ($order->order_type != OrderClose::TYPE_RECHARGE) {
                return $this->_error(2501, '不支持该类型的订单关闭');
            }

            if ($order->status != OrderClose::STATUS_PENDING) {
                return $this->_error(2501, '该订单状态无法关闭');
            }

            if (!in_array($order->order_subtype, OrderClose::$allowCloseSubtype)) {
                return $this->_error(2501, '该充值类型不支持关闭');
            }

            $order->close();

            return $this->_return('ok');
        } else {
            return $this->_error(2501);
        }
    }

    /**
     * 提现确认
     */
    public function cashVerify($uid, $username)
    {
        $result = ['status' => true, 'info' => ''];
        $info = UserInfo::getInfo($uid);
        if ($info->verifyReal()) {
            if ($info->real_name == $username) {
                $result['status'] = false;
                $result['info'] = '只能提现到本人银行卡';
            }
        } else {
            $result['status'] = false;
            $result['info'] = '提现前请先实名认证';
        }

        return $result;
    }
}