<?php
/**
 * Created by PhpStorm.
 * User: huawei
 * Date: 2017/6/16
 * Time: 9:25
 */

namespace passport\modules\inside\controllers;


use passport\modules\inside\models\Order;
use Yii;
use yii\base\Exception;

class OrderController extends BaseController
{
    /**
     * 贷款入账
     *
     * uid\platform_order_id\amount\desc
     *
     */
    public function actionLoan()
    {
        $model = new Order();
        $model->order_type = Order::TYPE_RECHARGE;
        $model->order_subtype = Order::SUB_TYPE_LOAN_RECORD;
        if ($model->load(Yii::$app->request->post(), '') && $model->save()) {// 创建贷款入账的充值订单
            $transaction = Yii::$app->db->beginTransaction();
            try {
                if (!$model->userBalance->plus($model->amount)) {
                    throw new Exception('添加用户可用余额失败');
                }

                if (!$model->setOrderSuccess()) {
                    throw new Exception('更新贷款入账充值订单状态失败');
                }

                if (!$consumeModel = $model->createLoanConsumeOrder()) {// 创建消费订单
                    throw new Exception('创建消费订单失败');
                }

                if (!$consumeModel->userBalance->less($consumeModel->amount)) {
                    throw new Exception('扣除用户可用余额失败');
                }

                if (!$consumeModel->userFreeze->plus($consumeModel->amount)) {
                    throw new Exception('增加用户冻结余额失败');
                }

                if (!$consumeModel->setOrderProcessing()) {
                    throw new Exception('更新贷款入账消费订单状态失败');
                }

                $transaction->commit();
                return $this->_return('贷款入账成功');
            } catch (Exception $e) {
                $transaction->rollBack();
                $model->setOrderFail();// 将订单设置为失败
                throw $e;
            }
        } else {
            return $this->_error(2401);
        }
    }
}