<?php
/**
 * Created by PhpStorm.
 * User: huawei
 * Date: 2017/6/17
 * Time: 18:18
 */

namespace passport\modules\pay\controllers;

use common\logic\RefundLogin;
use common\models\PoolBalance;
use common\models\PoolFreeze;
use passport\modules\pay\models\OrderForm;
use passport\traits\LoanTrait;
use Yii;
use passport\controllers\AuthController;
use yii\base\Exception;

class FreezeController extends AuthController
{
    use LoanTrait;

    /**
     * 贷款解冻
     * @return array
     * @throws Exception
     */
    public function actionThaw()
    {
        $orderIds = explode(',', Yii::$app->request->post('order_id'));
        $uid = Yii::$app->user->id;
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
     * @todo 前往erp验证退款金额是否正确
     */
    public function actionThawRefund()
    {
        $platform_order_id = Yii::$app->request->post('platform_order_id');
        $orderIds = explode(',', Yii::$app->request->post('order_id'));
        $uid = Yii::$app->user->id;
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
    }
}