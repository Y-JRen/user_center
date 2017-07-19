<?php

namespace passport\modules\sso\controllers;


use passport\modules\sso\models\UserOauth;
use Yii;
use yii\helpers\ArrayHelper;
use passport\helpers\Token;

class UserOauthController extends BaseController
{
    /**
     * 第三方登录
     * @return string
     */
    public function actionLoginOauth()
    {
        $data = Yii::$app->request->post();
        $open_id = ArrayHelper::getValue($data,'open_id');
        $type = ArrayHelper::getValue($data, 'type');

        $user_info = UserOauth::find()->where(['open_id' => $open_id, 'type' => $type])->one();

        if ($user_info) {
            $uid = ArrayHelper::getValue($user_info, 'uid');
            //生成一个token
            $about_token = new Token();
            return $about_token->createToken($uid);
        }else{
            return '登录失败';
        }
    }
}