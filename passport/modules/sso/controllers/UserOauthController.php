<?php

namespace passport\modules\sso\controllers;

use common\models\UserOauth;
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
            return $this->_return(['token' => Token::createToken($uid), 'uid' => $uid]);
        } else {
            return $this->_error(1004, '账号未绑定');
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

        /* @var $user_info UserOauth */
        $user_info = UserOauth::find()->where(['open_id' => $open_id, 'type' => $type])->one();
        if ($user_info) {
            if ($user_info->uid != Yii::$app->user->id) {
                return $this->_error(1007, '该账号已绑定其他用户');
            }
        } else {
            $model = new UserOauth();
            $model->uid = Yii::$app->user->id;
            $model->open_id = $open_id;
            $model->type = $type;
            $model->created_at = time();

            if (!$model->save()) {
                return $this->_error(1007, current($model->getFirstErrors()));
            }
        }
        return $this->_return('账号绑定成功');
    }
}

