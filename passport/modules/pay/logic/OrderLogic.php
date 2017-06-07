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
        if($orderId) {
            return false;
        }
        $order = Order::findOne($orderId);
        $cashFee = ArrayHelper::getValue($param, 'out_trade_no');
        if(!empty($order) && $order->amount * 100 == $cashFee ) {
            $db = \Yii::$app->db;
            $transaction = $db->beginTransaction();
            try{
                //充值成功
                $order->status = 2;
                $order->save();
                if(!$order) {
                   new Exception('订单更新失败');
                }
                $userBalance = UserBalance::findOne($order->uid);
                $userBalance->amount += $order/100;
                if (!$userBalance->save()) {
                    if(!$order) {
                        new Exception('余额更新失败');
                    }
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