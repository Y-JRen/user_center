<?php

namespace passport\logic;

use passport\helpers\Config;
use yii;
use common\models\LogSms;
use common\logic\HttpLogic;

/**
 * 短信
 * Class ApiLogsLogic
 * @package api\logic
 */
class SmsLogic extends Logic
{
    /**
     * 短信相关的配置
     */
    private static $url = 'http://139.196.250.134:8090/smsApi!mt';//接口地址
    private static $user = '17798999998';//接口账号
    private static $appKey = '14f6bf67387c417087898d2a106c120d';//接口token
    private static $extCode = '362928';//扩展码

    /**
     * 语音短信相关的配置
     */
    private static $yy_url = 'http://139.224.34.60:8099/api/playSoundMsg';
    private static $yy_appId = '4e1dfdf2a9c2459e9c5d0b3e8c0b85ef';
    private static $yy_apptoken = 'e6cd6def0fcf4c71942c810aff1561ef';

    protected $sms_tpl = [
        '1' => '【车城】您好，您的验证码是%s。退订回T',//注册短信
        '2' => '【车城】您好，您的验证码是%s。退订回T',//注册短信
    ];

    /**
     * 发送短信
     * @param int $tpl_index 短信模版id
     * @param string $phone 手机号
     * @param string $code 验证码
     *
     * @return boolean
     */
    public function send($tpl_index, $phone, $code = null)
    {
        if (!$this->_frequencyLimit($phone)) {
            return false;
        }

        if (!array_key_exists($tpl_index, $this->sms_tpl)) {
            return false;
        }

        yii::$app->redis->set("sms:{$tpl_index}:$phone", $code);
        $strContent = sprintf($this->sms_tpl[$tpl_index], $code);
        return $this->sendSms($phone, $strContent);
    }

    /**
     * 验证短信是否正确
     * @param int $tpl_index 短信模版id
     * @param string $code 验证码
     * @param string $phone 手机号
     *
     * @return boolean
     */
    public function checkCode($tpl_index, $code, $phone)
    {
        $_code = yii::$app->redis->get("sms:{$tpl_index}:$phone");
        if ($code == $_code) {
            yii::$app->redis->del("sms:{$tpl_index}:$phone");
            return true;
        }
        return false;
    }

    /**
     * 发送短信
     * @param string $strPhone 手机号
     * @param string $strContent 内容
     * @return boolean
     */
    private function sendSms($strPhone, $strContent)
    {
        $arrPost = [
            'userAccount' => self::$user,
            'appKey' => self::$appKey,
            'extCode' => self::$extCode,
            'cpmId' => date('YmdHis') . rand(10000, 99999),
            'mobile' => $strPhone,
            'message' => $strContent
        ];
        $jsonRes = HttpLogic::instance()->http(self::$url, 'POST', $arrPost);

        $this->_frequencyRecode($strPhone);

        $arrRes = json_decode($jsonRes, true);
        $this->saveSmsLogs($strPhone, $strContent, $arrRes['respCode'] == 200 ? 1 : 0, $arrRes['respMsg']);
        return ($arrRes['respCode'] == 200 ? true : false);
    }


    /**
     * 保存短信记录
     * @param string $strPhone 手机号
     * @param string $strContent 短信内容
     * @param int $resCode 成功失败（1：成功   0：失败）
     * @param string $resMsg 接口返回说明
     */
    private function saveSmsLogs($strPhone, $strContent, $resCode, $resMsg)
    {
        $arrSave = [
            'phone' => $strPhone,
            'platform' => \passport\helpers\Config::getPlatform(),
            'info' => $strContent,
            'status' => $resCode,
            'resmsg' => $resMsg,
            'created_at' => time()
        ];
        $objSms = new LogSms();
        $objSms->load(['LogSms' => $arrSave]);
        $objSms->save();
    }

    /**
     * 短信发送请求频率限制
     * @param $phone
     * @return bool
     */
    private function _frequencyLimit($phone)
    {
        $domain = Config::getRequestAllParams('domain');
        $projectConf = Config::params($domain);// 获取各个平台的配置

        $key = "SMS_DOMAIN_{$domain}_phone_{$phone}_" . strtotime(date('Y-m-d H:i'));
        $number = Yii::$app->redis->get($key);
        $number = (empty($number) ? 0 : $number);
        $confTime = yii\helpers\ArrayHelper::getValue($projectConf, 'sms', 0);

        // 平台有发送短信的配额，并且次数小于配额
        if ($confTime > 0 && ($number < $confTime)) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * 记录每分钟发送短信次数
     * @param $phone
     */
    private function _frequencyRecode($phone)
    {
        $domain = Config::getRequestAllParams('domain');
        $key = "SMS_DOMAIN_{$domain}_phone_{$phone}_" . strtotime(date('Y-m-d H:i'));
        Yii::$app->redis->incr($key);
    }


}