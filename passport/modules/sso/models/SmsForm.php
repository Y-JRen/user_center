<?php

namespace passport\modules\sso\models;


use Yii;
use yii\base\Model;
use passport\logic\SmsLogic;
use passport\logic\ImgcodeLogic;


class SmsForm extends Model
{
    public $phone;
    public $type;
    public $img_code;
    public $img_unique;

    public function rules()
    {
        return [
            [['phone', 'type', 'img_code', 'img_unique'], 'required', 'message' => '{attribute}不能为空'],
            ['phone', 'match', 'pattern' => '/^1\d{10}/', 'message' => '手机不正确'],
            ['img_code', 'validateCode']
        ];
    }

    public function validateCode($attribute, $params)
    {
        if (!$this->hasErrors()) {

            $bool = ImgcodeLogic::instance()->checkImgCode($this->$attribute, $this->img_unique);
            if (!$bool) {
                $this->addError($attribute, '验证码错误！');
            }
        }
    }

    /**
     * 发送验证码
     * @return array|bool
     */
    public function sendCode()
    {
        //验证码
        $code = rand(1001, 9999);
        $res = SmsLogic::instance()->send($this->type, $this->phone, ['code' => $code], 'luosimao');
        return $res;
    }

    public function sendNotify()
    {
        $params = Yii::$app->request->get('params', []);
        $res = SmsLogic::instance()->send($this->type, $this->phone, $params, 'luosimao');
        return $res;
    }
}