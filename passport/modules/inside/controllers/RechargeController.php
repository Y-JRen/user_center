<?php
/**
 * Created by PhpStorm.
 * User: legend
 * Date: 2017/8/23
 * Time: 上午11:44
 */

namespace passport\modules\inside\controllers;


use passport\modules\inside\models\Order;
use Yii;
use yii\helpers\ArrayHelper;

/**
 * 充值接口
 * Class RechargeController
 * @package passport\modules\inside\controllers
 */
class RechargeController extends BaseController
{
    public function actionLineDown()
    {
        $param = Yii::$app->request->post();
        if ($param['order_type'] != Order::TYPE_RECHARGE) {
            return $this->_error(2007);
        }

        if ($param['order_subtype'] != Order::SUB_TYPE_LINE_DOWN) {
            return $this->_error(2007);
        }

        $remark = [];
        $keys = ['payType', 'transferDate', 'amount', 'referenceNumber', 'bankName', 'bankCard', 'accountName', 'referenceImg'];

        foreach ($keys as $key) {
            if ($value = Yii::$app->request->post($key)) {
                $remark[$key] = $value;
            }
        }

        $model = new Order();
        $param['notice_status'] = 4;

        if (!empty($remark)) {
            $param['remark'] = json_encode($remark);
        }

        if ($model->load($param, '') && $model->save()) {
            $data['platform_order_id'] = $model->platform_order_id;
            $data['order_id'] = $model->order_id;
            $data['notice_platform_param'] = $model->notice_platform_param;

            return $this->_return($data);
        } else {
            return $this->_error(2001, current($model->getFirstErrors()));
        }
    }

    /**
     * 内部拉卡拉充值订单生成
     * @return array
     */
    public function actionLakala()
    {
        $param = Yii::$app->request->post();
        if ($param['order_type'] != Order::TYPE_RECHARGE) {
            return $this->_error(2007);
        }

        if ($param['order_subtype'] != Order::SUB_TYPE_LAKALA) {
            return $this->_error(2007);
        }

        if (empty(ArrayHelper::getValue($param, 'platform_order_id'))) {
            return $this->_error(2007);
        }

        $model = new Order();
        $param['notice_status'] = 4;
        if ($model->load($param, '') && $model->save()) {
            $data['platform_order_id'] = $model->platform_order_id;
            $data['order_id'] = $model->order_id;
            $data['notice_platform_param'] = $model->notice_platform_param;

            return $this->_return($data);
        } else {
            return $this->_error(2001, current($model->getFirstErrors()));
        }
    }
}