<?php
namespace passport\modules\sso\controllers;


class SmsController extends BaseController
{
    public function actionIndex()
    {
        
    }
    
    public function actionGetMsg()
    {
        $phone = \yii::$app->request->get('phone');
        return $this->_return('成功');
    }
}