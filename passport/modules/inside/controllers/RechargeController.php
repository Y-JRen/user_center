<?php
/**
 * Created by PhpStorm.
 * User: legend
 * Date: 2017/8/23
 * Time: 上午11:44
 */

namespace passport\modules\inside\controllers;


use passport\modules\inside\models\Order;
use passport\modules\pay\logic\PayLogic;
use Yii;
use yii\helpers\ArrayHelper;

/**
 * 充值接口
 * Class RechargeController
 * @package passport\modules\inside\controllers
 */
class RechargeController extends BaseController
{
    /**
     * 总充值入口
     * @return array
     */
    public function actionIndex()
    {
        $param = Yii::$app->request->post();
        return $this->recharge($param);
    }

    /**
     * 线下充值
     * @return array
     */
    public function actionLineDown()
    {
        $param = Yii::$app->request->post();
        $param['order_subtype'] = Order::SUB_TYPE_LINE_DOWN;

        return $this->recharge($param);
    }

    /**
     * 内部拉卡拉充值订单生成
     * @return array
     */
    public function actionLakala()
    {
        $param = Yii::$app->request->post();
        if (empty(ArrayHelper::getValue($param, 'platform_order_id'))) {
            return $this->_error(2007);
        }

        $param['order_subtype'] = Order::SUB_TYPE_LAKALA;

        return $this->recharge($param);
    }

    /**
     * 公共方法
     * @param $param
     * @return array
     */
    private function recharge($param)
    {
        $param['order_type'] = Order::TYPE_RECHARGE;
        $model = new Order();
        $param['notice_status'] = 4;
        $model->load($param, '');
        $model->initSet();
        if ($data = $model->checkOld()) {
            return $this->_return($data);
        } else {
            if ($model->save()) {
                $result = PayLogic::instance()->pay($model);

                $status = ArrayHelper::getValue($result, 'status', 0);
                $data = ArrayHelper::getValue($result, 'data');
                if ($status == 0) {
                    $data['platform_order_id'] = $model->platform_order_id;
                    $data['order_id'] = $model->order_id;
                    $data['notice_platform_param'] = $model->notice_platform_param;

                    // 返回值加入缓存
                    $model->addCache($model->id, $data);
                }
                return $this->_return($data);
            } else {
                return $this->_error(2001, current($model->getFirstErrors()));
            }
        }
    }
}