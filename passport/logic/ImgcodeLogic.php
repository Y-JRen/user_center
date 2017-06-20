<?php
namespace passport\logic;

use yii;
use passport\helpers\Config;
use yii\helpers\ArrayHelper;

/**
 * 图形验证码
 * Class ApiLogsLogic
 * @package api\logic
 */
class ImgcodeLogic extends Logic
{
	/**
	 * 获得图形验证码
	 * @param string $unique
	 * @param object $controller
	 */
	public function getImgCode($unique,$controller)
	{

		$config = $this->getConfig($unique);
		
		$redis = yii::$app->redis;
		$code = $redis->get("ImgCode:{$unique}");
		if($code){
			$config['fixedVerifyCode'] = $code;
		}
		$Captcha = new yii\captcha\CaptchaAction('captcha',$controller,$config);
		
		$code = $Captcha->getVerifyCode(true);
		$redis = yii::$app->redis;
		$redis->set("ImgCode:{$unique}",$code);
		$redis->expire("ImgCode:{$unique}", 300 );
		return $Captcha->run();
	}
	/**
	 * 验证code
	 * @param string $code
	 * @param string $unique
	 */
	public function checkImgCode($code, $unique)
	{
		$redis = yii::$app->redis;
		$bool = $code == $redis->get("ImgCode:{$unique}");
		if($bool){
			$redis->del("ImgCode:{$unique}");
		}
		return $bool;
	}
	/**
	 * 获得unique
	 * @param array $config
	 * @return string unique
	 */
	public function getUnqiue($config)
	{
		$unique = Yii::$app->security->generateRandomString();
		$unique = md5($unique.time());
		
		$_config = $this->checkConfig($config);
		
		$redis = yii::$app->redis;
		$redis->set('ImgCodeConfig:'.$unique,json_encode($_config));
		$redis->expire('ImgCodeConfig:'.$unique,300);
		return $unique;
		
	}
	/**
	 * unique是否存在
	 * @param string $unique
	 * @return boolean
	 */
	public function checkUnique($unique)
	{
		$redis = yii::$app->redis;
		$config = $redis->get('ImgCodeConfig:'.$unique);
		if($config){
			return true;
		}
		return false;
	}
	
	protected function getConfig($unique)
	{
		$redis = yii::$app->redis;
		$config = $redis->get('ImgCodeConfig:'.$unique);
		return json_decode($config,1);
	}
	
	protected function checkConfig($config)
	{
		$_config = [
				'height' => ArrayHelper::getValue($config, 'height',50),
				'width' => ArrayHelper::getValue($config, 'width',80),
				'minLength' => ArrayHelper::getValue($config, 'length',5),
				'maxLength' => ArrayHelper::getValue($config, 'length',5),
		];
		return $_config;
	}
}