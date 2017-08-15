<?php

namespace passport\logic;

use passport\helpers\Config;
use yii;
use common\models\LogSms;
use common\logic\HttpLogic;
use yii\helpers\ArrayHelper;

/**
 * 短信
 * Class ApiLogsLogic
 * @package api\logic
 */
class SmsLogic extends Logic
{
    public static $str;
    /**
     * 短信相关的配置 - 快通平台
     */
    private static $url = 'http://139.196.250.134:8090/smsApi!mt';//接口地址
    private static $user = '17798999998';//接口账号
    private static $appKey = '14f6bf67387c417087898d2a106c120d';//接口token
    private static $extCode = '362928';//扩展码

    /**
     * 语音短信相关的配置 - 快通平台
     */
    private static $yy_url = 'http://139.224.34.60:8099/api/playSoundMsg';
    private static $yy_appId = '4e1dfdf2a9c2459e9c5d0b3e8c0b85ef';
    private static $yy_apptoken = 'e6cd6def0fcf4c71942c810aff1561ef';

    /**
     * 短信模板  - 快通平台
     */
    protected $sms_tpl = [
        '1' => '【车城】您好，您的验证码是%s。退订回T',//注册短信
        '2' => '【车城】您好，您的验证码是%s。退订回T',//注册短信
    ];

    private $sms_service = [
        'luosimao',//螺丝帽平台
        'kuaitong',//快通平台
    ];

    /**
     * luosimao 平台的相关配置
     */
    private static $luosimao_url = 'http://sms-api.luosimao.com/v1/send.json';
    private static $luosimao_api_key = 'aeb02e2eecec5183085837c2d1162758';
    protected $luosimao_sms_tpl = [
        '1' => [//电商平台短信模板
            '1' => '您好，您的验证码是%s。【车城】',//注册短信
            '2' => '您好，您的验证码是%s。【车城】',//注册短信
        ],
        '5' => [//租车平台短信模板
            '1' => '您好，您的验证码是%s。【车城】',//注册短信
            '2' => '您好，您的验证码是%s。【车城】',//注册短信
            '1000' => '我们向您提供的用车服务已完成，请及时支付订单，否则将影响您今后的个人征信度。如需帮助，请及时联系客服【车城】',
            '1001' => '我们向您提供的用车服务已完成，您的未结订单已超过24小时，请尽快支付，否则将影响您今后的个人征信度。如需帮助，请及时联系客服【车城】',
            '1002' => '已赠送金豆到您的账户请访问公众号查询【车城】',
            '1003' => '感谢您对我们的支持，价值{amount}元的租车里程银豆已放入您的车城账户，可以直接抵扣{distance}公里的里程费哦。近期我们已开通了国家数字家庭基地新站点，请关注我们的公众号了解，更多车型任您选——无忧用车，车城陪伴你共度校园车生活！【车城】',
            '1004' => '感谢您对我们的支持，赠送您价值{amount}元里程代金豆，可以直接抵扣租车里程费哦。了解更多，请关注我们的公众号，更多车型任您选！【车城】',
            '1005' => '车友们！车城快租近期开展邀请有礼活动，凡成功邀请TA注册为认证会员后，车城送TA{money}元租车豆，并奖励你  {amount}元租车豆，赶紧告诉你的推荐码给TA，叫上TA一起租车浪起！【车城】',
            '1006' => '感谢你参与“车城推荐有礼”活动，并成功推荐TA成为车城认证会员，您将获得价值 {amount} 元的里程豆奖励，分享越多优惠越大哟，还不赶快走起！车城伴你同行【车城】',
            '1007' => '尊敬的车友，雨天路滑，为了您和他人的安全，请放慢车速，并保持一定的车距，车城伴你同行！【车城】',
            '1008' => '尊敬的车友，我们已收到您的紧急处理信息，鉴于您的单方碰撞现场情况，暂时不影响后续车辆使用，你可继续用车，后续如需要出险的话，我们会第一时间通知您，感谢你的配合与支持！【车城】',
            '1009' => "用户：{mobile}，已处理了违章，请求审核。【车城】",
            '1010' => '用户：{mobile}，已上传租车及实名认证资料，请求认证。【车城】',
            '1011' => '用户：{mobile}，已上传实名认证资料，请求认证。【车城】',
            '1012' => '用户：{mobile}，已上传免押金认证资料，请求认证。【车城】',
            '1013' => '感谢您对我们的支持，价值 {amount} 元的租车里程银豆已放入您的车城账户，可以直接抵扣里程费哦。请关注我们的公众号了解，更多车型任您选——无忧用车，车城陪伴你共度车生活！【车城】',
            '1014' => '您好，您的消费退款{gold}金豆已到帐，请注册查收。【车城】',
            '1015' => '我们向您提供的用车服务已完成，您订单已逾期1周未支付，我公司将中止您的账号，并向相关征信机构上传本次的违约记录。如有疑问，请及时联系客服【车城】',
            '1016' => '您提交的实名认证材料已收到，经审核，您所提交的《持身份证自拍照》不够清晰，请重新上传，如有疑问，请拨打  {telephone}，感谢您对我们的支持！【车城】',
            '1017' => '您提交的实名认证材料已收到，经审核，您所提交的《驾驶证（正副页）照片》不清晰，请重新上传，如有疑问，请拨打 {telephone}，感谢您对我们的支持！【车城】',
            '1018' => '您提交的实名认证材料已收到，经审核，您所提交的《驾驶证（正副页）照片》缺少副页信息，请重新上传，如有疑问，请拨打 {telephone}，感谢您对我们的支持！【车城】',
            '1019' => '您提交的实名认证材料已收到，经审核，您所提交的《身份证照片（正面）》不清晰或上传不成功，请重新上传，如有疑问，请拨打 {telephone}，感谢您对我们的支持！【车城】',
            '1020' => '您提交的实名认证材料已收到，经审核，您所提交的《身份证照片（反面）》不清晰或上传不成功，请重新上传，如有疑问，请拨打 {telephone}，感谢您对我们的支持！【车城】',
            '1021' => '您提交的实名认证材料已收到，经审核，您所提交的《身份证照片》的正面和反面不清晰或上传不成功，请重新上传，如有疑问，请拨打 {telephone}，感谢您对我们的支持！【车城】',
            '1022' => '您提交的实名认证材料已收到，经审核，您所提交的《持身份证自拍照》及《驾驶证（正副页）照片》不符合认证要求，请重新上传，如有疑问，请拨打 {telephone}，感谢您对我们的支持！【车城】',
            '1023' => '您提交的实名认证材料已收到，经审核，您所提交的《驾驶证（正副页）》信息与本人身份信息不符，请重新上传，如有疑问，请拨打 {telephone}，感谢您对我们的支持【车城】',
            '1024' => '您提交的实名认证材料已收到，经审核，您所填写的真实姓名及身份证号码与《身份证照片》不符，请重新上传，如有疑问，请拨打 {telephone}，感谢您对我们的支持【车城】',
            '1025' => '尊敬的车城车友，您今天的拾金不昧行为已在车城快租平台中备案，平台特赠送你价值 {amount} 块的里程抵用豆作为奖励，感谢您对我们工作的支持，车城伴你同行！【车城】',
            '1026' => '您的订单已生效，为确保您的车位不被他人占用，驱车离开时请升起地锁（地锁遥控器在车内），祝您行程愉快！【车城】',
            '1027' => '各位车友注意了，近期我们后台发现部分汽油车油量度数异常，为了不影响他人使用，请广大车友洁身自爱，文明用车，如再次发现油量异常或偷油现象，我公司将移交相关部门进行立案侦查，感谢车友们对我们的支持！【车城】',
            '1028' => '您好！您的订单离预定还车时间还剩15分钟，请按时还车。如要延长您的用车时间，请提前续车！【车城】',
            '1029' => '您已超过默认的取车时间,系统将自动开始计费!【车城】',
            '1030' => '您的预计租车时间已到,系统将自动还车,请及时支付本次租车费用【车城】',
            '1031' => '您车辆的当前电量已低于35%,请合理安排后续行程,如需帮助请联系客服【车城】',
            '1032' => '有车提醒: {mobile} 现有车辆可租,请访问公众号【车城】'
        ]
    ];


    /**
     * 发送短信
     * @param int $tpl_index 短信模版id
     * @param string $phone 手机号
     * @param string $code 验证码
     *
     * @return boolean | array
     */
    public function send($tpl_index, $phone, $params, $sms_service_type = 'luosimao')
    {
        if (!$this->_frequencyLimit($phone)) {
            return ['err_code' => 997, 'msg' => "达到发送最高限制"];
        }

        if ($sms_service_type == 'luosimao') { //luosimao 平台
            $tpl = $this->luosimao_sms_tpl;

            $result = $this->getContent($tpl, $tpl_index, $params, $phone);

            if (is_array($result)) {
                return $result;
            }

            return $this->sendLuosimaoSms($phone, $result);
        } else { // 快通平台
            $tpl = $this->sms_tpl;

            $result = $this->getContent($tpl, $tpl_index, $params, $phone);

            if (is_array($result)) {
                return $result;
            }

            return $this->sendLuosimaoSms($phone, $result);
        }
    }


    /**
     *  生成短信发送模板
     * @param $tpl
     * @param $tpl_index
     * @param $params
     * @param $phone
     * @return array|mixed|string
     */
    public function getContent($tpl, $tpl_index, $params, $phone)
    {
        $platform = Config::getPlatform();
        $str = ArrayHelper::getValue($tpl, [$platform, $tpl_index]);

        if (empty($str)) {
            return ['err_code' => 997, 'msg' => "发送模板不存在"];
        }

        if ($platform == 1 && in_array($tpl_index, [1, 2])) {
            $code = ArrayHelper::getValue($params, 'code');
            yii::$app->redis->set("sms:{$tpl_index}:$phone", $code);
            Yii::$app->redis->expire("sms:{$tpl_index}:$phone", 3600);
            $strContent = sprintf($str, $code);
        } else {
            preg_match_all('/{(?P<valName>[a-z]+)}/', $str, $matches);

            $replace = [];
            foreach (ArrayHelper::getValue($matches, 'valName', []) as $val) {
                if ($data = ArrayHelper::getValue($params, $val)) {
                    $replace['search'][] = "{{$val}}";
                    $replace['replace'][] = $data;
                    continue;
                } else {
                    return ['err_code' => 997, 'msg' => "参数params[{$val}]" . (array_key_exists($val, $params) ? '值不能为空' : '不存在')];
                }
            }

            if (empty($replace)) {
                $strContent = $str;
            } else {
                $strContent = str_replace($replace['search'], $replace['replace'], $str);
            }
        }

        return $strContent;
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
     * 螺丝帽平台短信发送
     * @param string $strPhone
     * @param string $strContent
     * @return boolean
     */
    private function sendLuosimaoSms($strPhone, $strContent)
    {
        $arrPost = [
            'mobile' => $strPhone,
            'message' => $strContent
        ];

        HttpLogic::instance()->setopt_param = [
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_0,
            CURLOPT_HEADER => FALSE,
            CURLOPT_HTTPAUTH => CURLAUTH_BASIC,
            CURLOPT_USERPWD => 'api:key-' . self::$luosimao_api_key,
        ];
        $jsonRes = HttpLogic::instance()->http(self::$luosimao_url, 'POST', $arrPost);
        $this->_frequencyRecode($strPhone);

        $arrRes = json_decode($jsonRes, true);
        $this->saveSmsLogs($strPhone, $strContent, $arrRes['error'] == 0 ? 1 : 0, $arrRes['msg'] . ' 【luosimao_】');
        return ($arrRes['error'] == 0 ? true : false);
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