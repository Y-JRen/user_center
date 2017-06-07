<?php
namespace passport\modules\sso\controllers;


use passport\logic\SmsLogic;
use yii;
use yii\helpers\ArrayHelper;

class SmsController extends BaseController
{
    public function actionIndex()
    {
        
    }
    /**
     * 发送短信
     */
    public function actionGetMsg()
    {
    	$get = yii::$app->request->get();
    	
        $phone = ArrayHelper::getValue($get,'phone',null);
        $type = ArrayHelper::getValue($get,'type',-1);
        
        if(!$phone){
        	return $this->_error(999);
        }
        //验证码
        $code = rand(1001,9999);
        $res = SmsLogic::instance()->send($type, $phone, $code);
        if($res){
            return $this->_return('成功');
        }
        return $this->_error(997);
    }
}