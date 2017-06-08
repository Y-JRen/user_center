<?php
namespace passport\modules\sso\controllers;

use passport\logic\ImgcodeLogic;

class ImgcodeController extends BaseController
{	
	public function actionGetImg()
	{
		$config = [
						'height' => 50,
						'width' => 80,
						'minLength' => 5,
						'maxLength' => 5
				];
		echo ImgcodeLogic::instance()->getImgCode($config, $this);
	}
}