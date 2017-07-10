<?php
/**
 * Created by PhpStorm.
 * User: huawei
 * Date: 2017/7/10
 * Time: 18:30
 */

namespace common\lib\pay\lakala;

use Yii;
use yii\helpers\ArrayHelper;

class LakalaCore extends yii\base\Object
{
    public $publicKeyPath;

    public function verifyNotify()
    {
        $post = Yii::$app->request->post();
        if (empty($post)) {
            return false;
        } else {
            $data = ArrayHelper::getValue($post, 'data');
            $sign = ArrayHelper::getValue($post, 'sign');

            // 生成验签字符串
            $dataArr = json_decode($data, true);
            $content = $this->createLinkString($dataArr);

            // 解析public_key
            $pubKey = file_get_contents($this->publicKeyPath);
            $res = openssl_get_publickey($pubKey);

            // 验证签名
            $result = (bool)openssl_verify($content, base64_decode($sign), $res);
            openssl_free_key($res);

            if ($result) {
                return true;
            } else {
                Yii::error('签名验证失败' . json_encode($post), 'verifyNotify');
                return false;
            }
        }
    }

    /**
     * 排序
     * @param $para
     * @return mixed
     */
    public function argSort($para)
    {
        ksort($para);
        reset($para);
        return $para;
    }

    /**
     * 拼接字符串
     * @param $para
     * @return bool|string
     */
    public function createLinkString($para)
    {
        $arg = "";
        while (list ($key, $val) = each($para)) {
            $arg .= $key . "=" . $val . "&";
        }
        //去掉最后一个&字符
        $arg = substr($arg, 0, count($arg) - 2);

        //如果存在转义字符，那么去掉转义
        if (get_magic_quotes_gpc()) {
            $arg = stripslashes($arg);
        }

        return $arg;
    }
}