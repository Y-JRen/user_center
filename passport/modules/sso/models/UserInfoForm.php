<?php
namespace passport\modules\sso\models;


use yii\base\Model;
use passport\helpers\Token;
use common\models\UserInfo;


class UserInfoForm extends Model
{
	public $real_name;
	public $card_number;
	
	public function rules()
	{
		return [
				[['real_name', 'card_number'],'required','message' => '{attribute}不能为空']
		];
	}
	
	
	public function realVerify($user)
	{
	    if(!$this->_verify()){
	        return ['status'=>false,'msg'=>'身份证不正确！'];
	    }
	    if(!$this->saveUserInfo($user)){
	        return ['status'=>false,'msg'=>'error！'];
	    }
		return ['status'=>true];
	}
	
	protected function saveUserInfo($user)
	{
	    $model = UserInfo::findOne($user->id);
	    if (empty($model)) {
	        $model = new UserInfo();
	        $model->uid = $user->id;
	    }
	    $model->real_name = $this->real_name;
	    $model->card_number = $this->card_number;
	    if($model->save()){
	        return true;
	    }
	    //var_dump($model->firstErrors);die();
	    return false;
	}
	
	protected function _verify()
	{
	    $pattern_1 = '/^[1-9]\d{7}((0\d)|(1[0-2]))(([0|1|2]\d)|3[0-1])\d{3}$/';
	    $pattern_2 = '/^[1-9]\d{5}[1-9]\d{3}((0\d)|(1[0-2]))(([0|1|2]\d)|3[0-1])\d{3}([0-9]|X)$/';
	    if(!preg_match($pattern_1, $this->card_number) && !preg_match($pattern_2, $this->card_number)){
	        return false;
	    }
	    return true;
	}
}