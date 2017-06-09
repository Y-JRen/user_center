<?php
namespace passport\modules\sso\controllers;

use passport\logic\ImgcodeLogic;
use yii;

class ImgcodeController extends BaseController
{	
	public function actionGetImg()
	{
		$get = yii::$app->request->get();
		$logic = ImgcodeLogic::instance();
		if(isset($get['unique']) && $logic->checkUnique($get['unique'])){
			echo $logic->getImgCode($get['unique'], $this);
		}else{
			$unique = $logic->getUnqiue($get);
			
			$data = ['unique'=>$unique,'url'=>"/sso/imgcode/get-img?unique={$unique}"];
			return $this->_return($data);
		}
	}
}