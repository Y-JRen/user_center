<?php
namespace passport\modules\sso\controllers;

use yii;
use yii\helpers\ArrayHelper;
use yii\filters\VerbFilter;
use passport\modules\sso\models\SmsForm;

class SmsController extends BaseController
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return ArrayHelper::merge(
            parent::behaviors(),
            [
                'verbs' => [
                    'class' => VerbFilter::className(),
                    'actions' => [
                        'get-msg' => ['get'],
                    ],
                ],
            ]
        );
    }

    public function actionIndex()
    {
        
    }
    /**
     * 发送短信
     */
    public function actionGetMsg()
    {
    	$get = yii::$app->request->get();
    	
    	$model = new SmsForm();
    	$model->load(['SmsForm'=>$get]);
    	if(!$model->validate()){
    		return $this->_error(1001,current($model -> getErrors())[0]);
    	}
    	$res = $model->send();
    	if($res){
    		return $this->_return('成功');
    	}else{
    		return $this->_error(997,$res['msg']);
    	}
    }

    /**
     * 临时获取短信验证码，取出所有验证
     * @return array
     */
    public function actionCode()
    {
        $model = new SmsForm();
        $model->load(Yii::$app->request->get(), '');
        $res = $model->send();

        if ($res) {
            return $this->_return('成功');
        } else {
            return $this->_error(997, $res['msg']);
        }
    }
}