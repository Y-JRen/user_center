<?php
/**
 * Created by PhpStorm.
 * User: legend
 * Date: 2017/9/12
 * Time: 下午6:26
 */

namespace passport\modules\inside\controllers;


use passport\modules\inside\models\CarHousekeeper;
use Yii;

class CarController extends BaseController
{
    public function verbs()
    {
        return [
            'hardware' => ['GET'],
            'device-check' => ['GET'],
            'device-lock' => ['POST'],
        ];
    }

    /**
     * 汽车智能硬件接口
     */
    public function actionHardware($uid)
    {
        $car = CarHousekeeper::find()->where(['uid' => $uid])->orderBy('id asc')->one();
        return $this->_return($car);
    }

    /**
     * 汽车智能硬件绑定设置检测
     */
    public function actionDeviceCheck($uid, $terminal_no, $client_device_no)
    {
        $status = false;
        if ($uid && $terminal_no && $client_device_no) {
            $model = CarHousekeeper::find()->where(['terminal_no' => $terminal_no])->one();
            $status = ($model && $model->uid == $uid && $model->client_device_no == md5($client_device_no));
        }
        return $this->_return($status);
    }

    /**
     * 汽车智能硬件客户端设备锁定
     */
    public function actionDeviceLock()
    {
        $status = false;
        $uid = Yii::$app->request->post('uid');
        $terminal_no = Yii::$app->request->post('terminal_no');
        $client_device_no = Yii::$app->request->post('client_device_no');

        if ($uid && $terminal_no && $client_device_no) {
            $model = CarHousekeeper::find()->where(['terminal_no' => $terminal_no])->one();
            if ($model && $model->uid == $uid) {
                $model->client_device_no = md5($client_device_no);
                $status = $model->save();
            }
        }

        return $this->_return($status);
    }
}