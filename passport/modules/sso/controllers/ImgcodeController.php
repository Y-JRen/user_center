<?php
namespace passport\modules\sso\controllers;

use passport\logic\ImgcodeLogic;
use yii;
use yii\helpers\Url;

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
			$url = Url::to(['/sso/imgcode/get-img', 'unique' => $unique], true);
			$data = ['unique'=>$unique,'url'=> $url];
			return $this->_return($data);
		}
	}
}