<?php
/**
 * Created by PhpStorm.
 * User: legend
 * Date: 2017/8/8
 * Time: 上午9:48
 */

namespace passport\modules\inside\controllers;

use common\models\PoolBalance;
use passport\modules\inside\models\Order;
use Yii;
use yii\base\Exception;


/**
 * 内部消费接口
 *
 * 内部消费接口不走先冻结再解冻流程，直接扣除余额
 *
 * @package passport\modules\inside\controllers
 */
class ConsumeController extends BaseController
{
    /**
     * @return array
     * @throws Exception
     */
    public function actionIndex()
    {
        $param = Yii::$app->request->post();
        if ($param['order_type'] != Order::TYPE_CONSUME) {
            return $this->_error(2007);
        }
        $model = new Order();
        $param['notice_status'] = 4;
        $model->load($param, '');
        $model->initSet();
        if ($model->save()) {

            $transaction = Yii::$app->db->beginTransaction();
            try {
                if (!$model->userBalance->less($model->amount)) {
                    throw new Exception('扣除用户余额失败');
                }

                if (!$model->addPoolBalance(PoolBalance::STYLE_LESS)) {
                    throw new Exception('添加资金流水记录失败');
                }

                if (!$model->setOrderSuccess()) {
                    throw new Exception('更新消费订单状态失败');
                }
                $transaction->commit();

                $data['platform_order_id'] = $model->platform_order_id;
                $data['order_id'] = $model->order_id;
                $data['notice_platform_param'] = $model->notice_platform_param;

                return $this->_return($data);
            } catch (Exception $e) {
                $model->exceptionHandle();
                $transaction->rollBack();
                throw $e;
            }
        } else {
            return $this->_error(2101, current($model->getFirstErrors()));
        }
    }
}