<?php
/**
 * Created by PhpStorm.
 * User: xiongjun
 * Date: 2017/6/6
 * Time: 17:58
 */

namespace passport\controllers;


use common\lib\pay\lakala\LakalaCore;
use common\lib\pay\wechat\PayCore;
use common\logic\ApiLogsLogic;
use dosamigos\qrcode\QrCode;
use passport\modules\pay\logic\OrderLogic;
use Yii;
use yii\web\Controller;

/**
 *
 * 公共接口
 * Class DefaultController
 * @package passport\controllers
 */
class DefaultController extends Controller
{
    public $enableCsrfValidation = false;

    /**
     * 生成图片二维码
     *
     * @param $url string
     */
    public function actionQrcode($url)
    {
        return QrCode::jpg($url);
    }

    /**
     * 微信回调
     *
     */
    public function actionWechatNotify()
    {
        $xml = Yii::$app->request->getRawBody();


        $pay = PayCore::instance();
        $data = $pay->xmlToArray($xml);
        if (empty($data)) {
            $return = [
                'return_code' => 'FAIL',
                'return_msg' => '参数错误'
            ];
        } else {
            $sign = $data['sign'];
            unset($data['sign']);
            if ($sign == $pay->sign($data)) {
                $result = OrderLogic::instance()->notify($data);
                if ($result) {
                    $return = [
                        'return_code' => 'SUCCESS',
                        'return_msg' => 'OK'
                    ];
                } else {
                    $return = [
                        'return_code' => 'FAIL',
                        'return_msg' => '订单更新失败'
                    ];
                }

            } else {
                $return = [
                    'return_code' => 'FAIL',
                    'return_msg' => '签名失败'
                ];
            }
        }
        header("Content-type:text/xml");
        echo $pay->buildXml($return);
        exit();
    }


    /**
     * 微信回调补救
     *
     */
    public function actionWechatNotifyBak()
    {
        $json = Yii::$app->request->post('json');
        $data = json_decode($json, true);
        $pay = PayCore::instance();
        if (empty($data)) {
            $return = [
                'return_code' => 'FAIL',
                'return_msg' => '参数错误'
            ];
        } else {
            $sign = $data['sign'];
            unset($data['sign']);
            if ($sign == $pay->sign($data)) {
                $result = OrderLogic::instance()->notify($data);
                if ($result) {
                    $return = [
                        'return_code' => 'SUCCESS',
                        'return_msg' => 'OK'
                    ];
                } else {
                    $return = [
                        'return_code' => 'FAIL',
                        'return_msg' => '订单更新失败'
                    ];
                }

            } else {
                $return = [
                    'return_code' => 'FAIL',
                    'return_msg' => '签名失败'
                ];
            }
        }
        header("Content-type:text/xml");
        echo $pay->buildXml($return);
        exit();
    }

    /**
     * 拉卡拉回调
     */
    public function actionLakalaNotify()
    {
        $post = Yii::$app->request->post();

        ApiLogsLogic::instance()->addLogs('lakala.data', json_encode($post));

        $lakala = new LakalaCore(['publicKeyPath' => Yii::$app->params['pay']['lakala']['public_key']]);
        $result = $lakala->verifyNotify();

        if ($result && OrderLogic::instance()->lakalaNotify($post)) {
            echo 'success';
        } else {
            echo 'fail';
        }
    }
}