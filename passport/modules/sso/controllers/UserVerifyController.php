<?php
namespace passport\modules\sso\controllers;


use yii;
use passport\controllers\AuthController;
use passport\modules\sso\models\UserInfoForm;

class UserVerifyController extends AuthController
{
	public function actionVerify()
	{
		$data['UserInfoForm'] = yii::$app->request->post();
		$model = new UserInfoForm();
		$model->load($data);
		if( !$model->validate()){
			return $this->_error(990,current($model -> getErrors())[0]);
		}
		$user = yii::$app->user->identity;
		$res = $model -> realVerify($user);
		if($res['status']){
			return $this->_return('成功');
		}
		return $this->_error(991,$res['msg']);
	}
}	