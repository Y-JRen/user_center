<?php
namespace passport\logic;

use yii;
use passport\helpers\Config;

/**
 * 图形验证码
 * Class ApiLogsLogic
 * @package api\logic
 */
class ImgcodeLogic extends Logic
{
	public function getImgCode($config,$controller)
	{
		$Captcha = new yii\captcha\CaptchaAction('captcha',$controller,$config);
	
		$plat = Config::getPlatform();//获取平台
		$client = Config::getClientType();//获取客户端类型
	
	
		$code = $Captcha->getVerifyCode(true);
		$redis = yii::$app->redis;
		$redis->set("ImgCode:{$plat}_{$client}",$code);
		$redis->expire($token, 300 );
		return $Captcha->run();
	}
	
	public function checkImgCode($code)
	{
		$plat = Config::getPlatform();//获取平台
		$client = Config::getClientType();//获取客户端类型
			
		$bool = $code == yii::$app->redis->get("ImgCode:{$plat}_{$client}");
		return $bool;
	}
}