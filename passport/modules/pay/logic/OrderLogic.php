<?php
/**
 * Created by PhpStorm.
 * User: xiongjun
 * Date: 2017/6/6
 * Time: 09:39
 */

namespace passport\modules\pay\logic;


use Yii;
use common\logic\Logic;
use common\models\Order;
use common\models\UserBalance;
use yii\db\Exception;
use yii\helpers\ArrayHelper;

/**
 * 订单类
 *
 * Class OrderLogic
 * @package passport\modules\pay\logic
 */
class OrderLogic extends Logic
{
    /**
     * 更新订单状态
     *
     * @param array $param
     *
     * @return boolean
     * @throws Exception
     */
    public function notify($param)
    {
        $orderId = ArrayHelper::getValue($param, 'out_trade_no');
        if (!$orderId) {
            return false;
        }
        $order = Order::findOne(['order_id' => $orderId]);
        $cashFee = ArrayHelper::getValue($param, 'cash_fee');
        if (!empty($order) && $order->amount * 100 == $cashFee) {
            $db = Yii::$app->db;
            $transaction = $db->beginTransaction();
            try {
                //充值成功
                $order->status = 2;
                $order->save();
                if (!$order) {
                    throw new Exception('订单更新失败');
                }
                $userBalance = UserBalance::findOne($order->uid);
                if (!$userBalance) {
                    $userBalance = new UserBalance();
                    $userBalance->uid = $order->uid;
                }
                $userBalance->amount += $cashFee / 100;
                $userBalance->updated_at = time();
                if (!$userBalance->save()) {
                    throw new Exception('余额更新失败', $userBalance->errors);
                }
                $transaction->commit();
                return true;
            } catch (Exception $e) {
                $transaction->rollBack();
                throw $e;

            }
        }
        return false;
    }

    public function alipayNotify($params)
    {
        $orderId = ArrayHelper::getValue($params, 'out_trade_no');// 商家订单号
        if (!$orderId) {
            return false;
        }
        $order = Order::findOne(['order_id' => $orderId]);
        $amount = ArrayHelper::getValue($params, 'total_amount');// 订单金额
        if ($order && $order->amount == $amount) {
            $transaction = Yii::$app->db->beginTransaction();
            try {
//                $order->remark = json_encode(['trade_no' => ArrayHelper::getValue($params, 'trade_no')]);// 将支付宝交易号，写入备注中
                if (!$order->setOrderSuccess())// 更新订单状态
                {
                    throw new Exception('订单更新失败');
                }

                if (!$order->userBalance->plus($order->amount)) {
                    throw new Exception('余额添加失败');
                }

                if ($order->remark == 'quick_pay') {// 快捷支付
                    // @todo 立刻添加一条消费记录
                }

                $transaction->commit();
                return true;
            } catch (Exception $e) {
                $transaction->rollBack();
                throw $e;
            }
        }
        return false;
    }
}