<?php
/**
 * Created by PhpStorm.
 * User: xiongjun
 * Date: 2017/6/6
 * Time: 09:39
 */

namespace passport\modules\pay\logic;


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
        if(!$orderId) {
            return false;
        }
        $order = Order::findOne(['order_id' => $orderId]);
        $cashFee = ArrayHelper::getValue($param, 'cash_fee');
        if(!empty($order) && $order->amount * 100 == $cashFee ) {
            $db = \Yii::$app->db;
            $transaction = $db->beginTransaction();
            try{
                //充值成功
                $order->status = 2;
                $order->save();
                if(!$order) {
                   throw new Exception('订单更新失败');
                }
                $userBalance = UserBalance::findOne($order->uid);
                if(!$userBalance) {
                    $userBalance = new UserBalance();
                    $userBalance->uid = $order->uid;
                }
                $userBalance->amount += $cashFee /100;
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

    /**
     * 消费
     *
     * @param Order $order
     */
    public function consume($order)
    {
        $userBalance = UserBalance::findOne($order->uid);
    }
}