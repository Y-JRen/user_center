<?php

namespace passport\modules\sso\controllers;


use passport\modules\sso\models\UserInfo;
use yii;
use passport\controllers\AuthController;

class UserVerifyController extends AuthController
{
    public function actionVerify()
    {
        $model = UserInfo::find()->where(['uid' => Yii::$app->user->id])->one();
        if (empty($model)) {
            $model = new UserInfo(['uid' => Yii::$app->user->id, 'is_real' => 0]);
        }

        $model->scenario = UserInfo::SCENARIO_REAL;

        if ($model->load(Yii::$app->request->post(), '') && $model->save()) {
            return $this->_return('成功');
        } else {
            return $this->_error(990, current($model->getFirstErrors()));
        }
    }
}	