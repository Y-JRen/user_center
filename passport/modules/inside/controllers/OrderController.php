<?php
/**
 * Created by PhpStorm.
 * User: huawei
 * Date: 2017/6/16
 * Time: 9:25
 */

namespace passport\modules\inside\controllers;


use common\jobs\RechargePushJob;
use common\models\PoolBalance;
use common\models\PoolFreeze;
use passport\modules\inside\models\Order;
use Yii;
use yii\base\Exception;
use yii\helpers\ArrayHelper;

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

                if (!$model->addPoolBalance(PoolBalance::STYLE_PLUS)) {
                    throw new Exception('添加资金流水记录失败');
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

                if (!$consumeModel->addPoolBalance(PoolBalance::STYLE_LESS)) {
                    throw new Exception('添加资金流水记录失败');
                }

                if (!$consumeModel->userFreeze->plus($consumeModel->amount)) {
                    throw new Exception('增加用户冻结余额失败');
                }

                if (!$consumeModel->addPoolFreeze(PoolFreeze::STYLE_PLUS)) {
                    throw new Exception('添加冻结资金流水记录失败');
                }

                if (!$consumeModel->setOrderProcessing()) {
                    throw new Exception('更新贷款入账消费订单状态失败');
                }

                $transaction->commit();
                return $this->_return(['order_id' => $consumeModel->order_id], 0, '贷款入账成功');
            } catch (Exception $e) {
                $transaction->rollBack();
                $model->setOrderFail();// 将订单设置为失败
                throw $e;
            }
        } else {
            Yii::error(var_export($model->getErrors(), true), 'actionLoan');
            return $this->_error(2401);
        }
    }

    /**
     * 贷款解冻
     * @return array
     * @throws Exception
     */
    public function actionUnfreeze()
    {
        $order_id = Yii::$app->request->post('order_id');
        $uid = Yii::$app->request->post('uid');
        $amount = Yii::$app->request->post('amount');

        /* @var $order Order */
        $order = Order::find()->where(['order_id' => $order_id])->one();
        if (!$order) {
            return $this->_error(2005, '订单不存在');
        }

        if ($order->status != Order::STATUS_PROCESSING) {
            return $this->_error(2005, '订单状态异常');
        }

        if ($order->uid != $uid || $order->amount != $amount) {
            return $this->_error(2005, '订单用户、订单金额不匹配');
        }

        $transaction = Yii::$app->db->beginTransaction();
        try {
            if (!$order->userFreeze->less($amount)) {
                throw new Exception('用户冻结余额解冻失败');
            }

            if (!$order->addPoolFreeze(PoolFreeze::STYLE_LESS)) {
                throw new Exception('添加冻结资金流水记录失败');
            }

            if (!$order->setOrderSuccess()) {
                throw new Exception('更新订单状态失败');
            }

            $transaction->commit();
            return $this->_return('冻结金额解冻成功');
        } catch (Exception $e) {
            $transaction->rollBack();
            throw $e;
        }
    }


    /**
     * @return array
     * @throws Exception
     */
    public function actionBill()
    {
        $back_order = Yii::$app->request->post('back_order');
        $transaction_time = Yii::$app->request->post('transaction_time');// Y-m-d H:i:s 格式

        $subtype = Yii::$app->request->post('subtype');
        if (!in_array($subtype, ['tmall'])) {
            return $this->_error(2007, '类型参数有误');
        }

        $method = Yii::$app->request->post('method', 1);
        if (!in_array($method, [1, 2, 3])) {
            return $this->_error(2007, '充值方式参数有误');
        }

        $transaction = Yii::$app->db->beginTransaction();
        try {
            // 添加充值订单
            $recharge = new Order();
            $recharge->order_type = Order::TYPE_RECHARGE;
            $recharge->order_subtype = $subtype;
            $recharge->notice_status = 4;
            $recharge->load(Yii::$app->request->post(), '');

            if (!$recharge->save()) {
                Yii::error(var_export($recharge->errors, true), 'actionBill');
                throw new Exception('保存充值订单失败');
            }

            if (!$recharge->userBalance->plus($recharge->amount)) {
                throw new Exception('添加用户可用余额失败');
            }

            if (!$recharge->setOrderSuccess()) {
                throw new Exception('更新充值订单状态失败');
            }

            // 添加消费订单
            $consumeArr = [
                'order_subtype' => $subtype,
                'desc' => "订单号：[{$recharge->platform_order_id}]的天猫消费订单"
            ];

            if (!$consumeModel = $recharge->createConsumeOrder($consumeArr)) {
                throw new Exception('创建消费订单失败');
            }

            if (!$consumeModel->userBalance->less($consumeModel->amount)) {
                throw new Exception('扣除用户可用余额失败');
            }

            if (!$consumeModel->userFreeze->plus($consumeModel->amount)) {
                throw new Exception('增加用户冻结余额失败');
            }

            if (!$consumeModel->userFreeze->less($consumeModel->amount)) {
                throw new Exception('扣除用户冻结余额失败');
            }

            if (!$consumeModel->setOrderSuccess()) {
                throw new Exception('更新消费订单状态失败');
            }

            $transaction->commit();

            // 添加充值到账的记录,并推送到财务系统
            Yii::$app->queue_second->push(new RechargePushJob([
                'back_order' => $back_order,
                'order_id' => $recharge->order_id,
                'amount' => $recharge->amount,
                'transaction_time' => $transaction_time,
                'method' => $method,
                'uid' => $recharge->uid,
            ]));

            return $this->_return('处理成功');
        } catch (Exception $e) {
            $transaction->rollBack();
            throw $e;
        }
    }
}