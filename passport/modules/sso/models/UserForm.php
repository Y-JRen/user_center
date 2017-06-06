<?php
namespace passport\modules\sso\models;


use yii\base\Model;
use common\models\User;
use passport\helpers\Token;
use passport\helpers\Config;


class UserForm extends Model
{
	/**注册场景*/
	const SCENARIO_REG = 'reg';
	/**登入场景*/
	const SCENARIO_LOGIN = 'login';
	
	
	public $user_name;
	public $passwd;
	public $repasswd;
	public $verify_code;
	public $channel;
	public $is_agreement;
	
	
	public function rules()
	{
		return [
				[	
					['user_name', 'passwd'], 
					'required',
					'on' => [self::SCENARIO_REG,self::SCENARIO_LOGIN],
					'message' => '{attribute}不能为空'
				],
				[
					['repasswd', 'verify_code', 'channel','is_agreement'], 
					'required',
				    'on' => [self::SCENARIO_REG],
					'message' => '{attribute}不能为空'
				],
		        
				['user_name', 'string', 'max' => 12,'message'=>'手机号不正确'],
		        ['user_name','unique','targetClass' => '\common\models\User','on'=> [self::SCENARIO_REG], 'message' => '手机号存在.'],
				['passwd', 'string', 'length' => 32,'message'=>'密码格式错误'],
				//['passwd', 'string', 'min' => 6,'on' => [self::SCENARIO_REG],'message'=>'密码不能低于6位'],
				['repasswd', 'compare', 'compareAttribute' => 'passwd','message' => '两次输入的密码不一致'],
				['verify_code','validateCode'],
		        ['is_agreement','integer', 'message' => '必需同意协议']
		];
	}
	
	public function scenarios()
	{
		return [
				self::SCENARIO_REG => ['user_name', 'passwd', 'repasswd','verify_code','channel','is_agreement'],
				self::SCENARIO_LOGIN => ['user_name', 'passwd'],
		];
	}
	/**
	 * 验证码验证
	 * @param unknown $attribute
	 * @param unknown $params
	 */
	public function validateCode($attribute, $params)
	{
		if (!$this->hasErrors()) {
		    if($this->$attribute != '1234'){
			     $this->addError($attribute, '验证码错误！');
		    }
		}
	}
	
	public function login($user_id)
	{
		//create token
		$token = Token::encodeToken($user_id,time(),Config::getPlatform());
		//\Yii::$app->redis->set($token,$user_id,'EX 2592000');
		return $token;
	}
	
	public function reg()
	{
		$model = new User();
		$model->phone = $this->user_name;
		$model->user_name = $this->user_name; 
		$model->email = ''; 
		$model->passwd = $this->encyptPasswd($this->passwd); 
		$model->from_platform = Config::getPlatform();
		$model->from_channel = $this->getFrom(); 
		$model->reg_time = time(); 
		$model->reg_ip = $this->getIp(); 
		$model->login_time = 0;
		if(!$model -> insert()){
		    return ['status' => false, 'msg' => current($model->getErrors())[0]];
		}else{
		    return ['status' => true, 'user_id' => $model->id];
		}
	}
	/**
	 * 登入验证
	 */
	public function checkLogin()
	{
	    $model = new User();
	    $user = $model->findOne(['phone' => $this->user_name]);
	    if(!$user){
	        return ['status'=>false,'msg'=>'用户不存在'];
	    }
	    if($user->passwd != $this->encyptPasswd($this->passwd)){
	        return ['status'=>false,'msg'=>'密码不正确'];
	    }
	    return ['status'=>true,'user_id' => $user->id];
	}
	/**
	 * 获取来源
	 */
	protected function getFrom()
	{
	    return $this->channel;
	}
	/**
	 * 获取ip
	 */
	protected function getIp()
	{
	    return \yii::$app->request->userIP;
	}
	/**
	 * 密码加密
	 * @param unknown $passwd
	 */
	protected function encyptPasswd($passwd)
	{
	    $salt = '$*I_$%@#Abc^!';
	    $tmp = substr($passwd, -6);
	    $password = md5($passwd.$salt.$tmp);
	    return $password;
	}
}