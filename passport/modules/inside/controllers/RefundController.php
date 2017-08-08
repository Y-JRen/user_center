<?php
/**
 * Created by PhpStorm.
 * User: legend
 * Date: 2017/8/8
 * Time: 下午2:07
 */

namespace passport\modules\inside\controllers;


use common\models\PoolBalance;
use passport\modules\inside\models\Order;
use Yii;
use yii\base\Exception;

class RefundController extends BaseController
{

    public function actionIndex()
    {
        $param = Yii::$app->request->post();
        if ($param['order_type'] != Order::TYPE_REFUND) {
            return $this->_error(2007);
        }

        /* rules 已有验证
         * if (empty(Yii::$app->request->post('platform_order_id'))) {
            return $this->_return(null, 2007, '平台ID不存在');
        }*/

        $model = new Order();
        $param['notice_status'] = 4;
        if ($model->load($param, '') && $model->save()) {
            $transaction = Yii::$app->db->beginTransaction();
            try {
                if (!$this->refundCheck($model->platform_order_id)) {
                    throw new Exception('该笔订单，已有退款记录');
                }

                if (!$model->userBalance->plus($model->amount)) {
                    throw new Exception('余额增加失败');
                }

                // 添加资金流水记录
                if (!$model->addPoolBalance(PoolBalance::STYLE_PLUS)) {
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
                $model->setOrderFail();
                throw $e;
            }
        } else {
            Yii::error(var_export($model->errors, true));
            return $this->_error(2201);
        }
    }


    /**
     * 查看该电商订单号是否有过退款操作
     * 为true时可以退款
     *
     * @param $platform_order_id
     * @return bool
     */
    public function refundCheck($platform_order_id)
    {
        $count = Order::find()->where([
            'order_type' => Order::TYPE_REFUND,
            'platform_order_id' => $platform_order_id,
            'status' => Order::STATUS_SUCCESSFUL,
        ])->count();

        return empty($count);
    }
}