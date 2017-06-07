<?php
/**
 * Created by PhpStorm.
 * User: xiongjun
 * Date: 2017/6/5
 * Time: 16:36
 */

namespace passport\modules\pay\controllers;


use passport\controllers\BaseController;
use passport\modules\pay\forms\OrderForm;
use passport\modules\pay\logic\PayLogic;
use yii\helpers\Url;

/**
 * 下单订单
 *
 * Class OrderController
 *
 * @package passport\modules\pay\controllers
 */
class OrderController extends BaseController
{
    /**
     * 生成订单
     *
     */
    public function actionIndex()
    {
        $param = \Yii::$app->request->post();
        $data['OrderForm'] = $param;
        //初始化订单状态默认位 1
        $data['OrderForm']['status'] = 1;
        $model = new OrderForm();
        if($model->load($data) && $model->save()){
            $result = PayLogic::instance()->pay($model);
            if($result['return_code'] == 'SUCCESS' && $result['result_code'] == 'SUCCESS') {
                $qrCode = 'http://'.$_SERVER['HTTP_HOST'].Url::to(['/default/qrcode', 'url' => $result['code_url']]);
                return $this->_return([
                    'order_id' => $model->order_id,
                    'qrcode' => $qrCode,
                    'platform_order_id' => $model->platform_order_id
                ]);
            }
            return $this->_return(2002 ,$result);
        } else {
            return $this->_error(2001, $model->errors);
        }
    }
}