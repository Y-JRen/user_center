<?php
/**
 * Created by PhpStorm.
 * User: xiongjun
 * Date: 2017/6/6
 * Time: 17:58
 */

namespace passport\controllers;


use common\lib\pay\wechat\PayCore;
use dosamigos\qrcode\QrCode;
use passport\modules\pay\logic\OrderLogic;
use yii\web\Controller;

/**
 *
 * 公共接口
 * Class DefaultController
 * @package passport\controllers
 */
class DefaultController extends Controller
{
    /**
     * 生成图片二维码
     *
     * @param $url
     */
    public function actionQrcode($url)
    {
        return QrCode::jpg($url);
    }

    /**
     * 二维码示例
     *
     */
    public function actionDemo()
    {
        echo "<img src='http://127.0.0.1:8081/default/qrcode?url=weixin://wxpay/bizpayurl?pr=5eYlGha'>";
    }

    /**
     * 微信回调
     *
     */
    public function actionWechatNotify()
    {
        $xml = $GLOBALS['HTTP_RAW_POST_DATA'];

        $pay = PayCore::instance();
        $data = $pay->xmlToArray($xml);
        $sign = $data['sign'];
        unset($data['sign']);
        if ($sign == $pay->sign($data)) {
            $result = OrderLogic::instance()->notify($data);
            if ($result) {
                $data = [
                    'return_code' => 'SUCCESS',
                    'return_msg' => 'OK'
                ];
            } else {
                $data = [
                    'return_code' => 'FAIL',
                    'return_msg' => '签名失败'
                ];
            }

        } else {
            $data = [
                'return_code' => 'FAIL',
                'return_msg' => '签名失败'
            ];
        }
        echo $pay->buildXml($data);exit();
    }
}