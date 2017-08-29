<?php
/**
 * Created by PhpStorm.
 * User: huawei
 * Date: 2017/6/16
 * Time: 9:25
 */

namespace passport\modules\inside\controllers;

use common\jobs\RechargePushJob;
use common\lib\pay\alipay\PayCore;
use common\lib\pay\alipay\PayQuery;
use common\logic\RefundLogin;
use common\models\PoolBalance;
use common\models\PoolFreeze;
use passport\helpers\Config;
use passport\modules\inside\models\Order;
use passport\traits\LoanTrait;
use Yii;
use yii\base\Exception;

class OrderController extends BaseController
{
    use LoanTrait;

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
        $orderIds = explode(',', Yii::$app->request->post('order_id'));
        $uid = Yii::$app->request->post('uid');
        $amount = Yii::$app->request->post('amount');

        $transaction = Yii::$app->db->beginTransaction();
        $result = $this->thaw($uid, $orderIds, $amount);

        if ($result['status']) {
            $transaction->commit();
            return $this->_return('冻结金额解冻成功');
        } else {
            $transaction->rollBack();
            return $this->_error(2005, $result['info']);
        }
    }

    /**
     * 贷款解冻并退款
     * @return array
     * @throws Exception
     */
    public function actionThawRefund()
    {
        $platform_order_id = Yii::$app->request->post('platform_order_id');
        $orderIds = explode(',', Yii::$app->request->post('order_id'));
        $uid = Yii::$app->request->post('uid');
        $amount = Yii::$app->request->post('amount');
        $refundAmount = Yii::$app->request->post('refund_amount');

        $transaction = Yii::$app->db->beginTransaction();

        $result = $this->thaw($uid, $orderIds, $amount);
        if ($result['status']) {
            unset($result);
            $result = $this->refund($uid, $refundAmount, $platform_order_id);
            if ($result['status']) {
                $transaction->commit();
                return $this->_return($result['info']);
            }
        }

        $transaction->rollBack();
        return $this->_error(2005, $result['info']);
    }

    /**
     * 天猫走账
     *
     * @todo 流水号查询
     *
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

            if (!$recharge->addPoolBalance(PoolBalance::STYLE_PLUS)) {
                throw new Exception('添加资金流水记录失败');
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

            if (!$consumeModel->addPoolBalance(PoolBalance::STYLE_LESS)) {
                throw new Exception('添加资金流水记录失败');
            }

            if (!$consumeModel->userFreeze->plus($consumeModel->amount)) {
                throw new Exception('增加用户冻结余额失败');
            }

            if (!$consumeModel->addPoolFreeze(PoolFreeze::STYLE_PLUS)) {
                throw new Exception('添加冻结资金流水记录失败');
            }

            if (!$consumeModel->userFreeze->less($consumeModel->amount)) {
                throw new Exception('扣除用户冻结余额失败');
            }

            if (!$consumeModel->addPoolFreeze(PoolFreeze::STYLE_LESS)) {
                throw new Exception('添加冻结资金流水记录失败');
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

    /**
     * tmall 订单号查询
     * @param $trade_no
     * @param $out_trade_no
     * @return bool|\SimpleXMLElement[]|string
     */
    public function orderQuery($trade_no, $out_trade_no = '')
    {
        $config = Config::getAliConfig('tmall');
        $pay = new PayCore($config);

        $query = new PayQuery();
        $query->setTradeNo($trade_no);
        $query->setOutTradeNo($out_trade_no);
        return $pay->query($query);
    }
}