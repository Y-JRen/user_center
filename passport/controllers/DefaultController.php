<?php
/**
 * Created by PhpStorm.
 * User: xiongjun
 * Date: 2017/6/6
 * Time: 17:58
 */

namespace passport\controllers;


use dosamigos\qrcode\QrCode;
use yii\web\Controller;

/**
 *
 * 公共接口
 * Class DefaultController
 * @package passport\controllers
 */
class DefaultController extends Controller
{
    public function actionQrcode($url)
    {
        return QrCode::jpg($url);
    }

    public function actionDemo()
    {
        echo "<img src='http://127.0.0.1:8081/default/qrcode?url=weixin://wxpay/bizpayurl?pr=5eYlGha'>";
    }
}