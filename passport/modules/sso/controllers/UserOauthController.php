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
    public function actionLogin()
    {
        $data = Yii::$app->request->post();
        $open_id = ArrayHelper::getValue($data, 'open_id');
        $type = ArrayHelper::getValue($data, 'type');

        $user_info = UserOauth::find()->where(['open_id' => $open_id, 'type' => $type])->one();

        if ($user_info) {
            $uid = ArrayHelper::getValue($user_info, 'uid');

            $about_token = new Token();
            return $this->_return(['token' => $about_token->createToken($uid), 'uid' => ArrayHelper::getValue($user_info, 'uid')]);
        } else {
            $info = ['info' => '用户不存在'];
            return $this->_return($info);
        }
    }

    /**
     * 账号绑定
     * @return array
     */
    public function actionBind()
    {
        $data = Yii::$app->request->post();
        $open_id = ArrayHelper::getValue($data, 'open_id');
        $type = ArrayHelper::getValue($data, 'type');
        $token = ArrayHelper::getValue($data, 'token');

        $user_info = UserOauth::find()->where(['open_id' => $open_id, 'type' => $type])->exists();
        if ($user_info) {
            $data = ['info' => '账号已绑定'];
            return $this->_return($data);
        } else {
            $model = new UserOauth();

            if(Token::getToken($token)){
                $model->uid = Token::getUid($token);
                $model->open_id = $open_id;
                $model->type = $type;

                if ($model->save()) {
                    return $this->_return(['info' => '账号绑定成功']);
                } else {
                    return $this->_return(['info' => '账号绑定失败']);
                }
            }else{
                return $this->_return(['info' => '获取用户信息失败']);
            }
        }
    }
}

