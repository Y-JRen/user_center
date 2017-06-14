<?php

namespace passport\modules\sso\controllers;


use passport\modules\sso\models\User;
use yii;
use passport\controllers\AuthController;

/**
 * 用户基础信息接口
 * 
 * Class UserVerifyController
 * @package passport\modules\sso\controllers
 */
class UserBaseController extends AuthController
{
    public function verbs()
    {
        return [
            'info' => ['get']
        ];
    }

    /**
     * 获取用户扩展信息接口
     *
     * @return array|null|yii\db\ActiveRecord
     */
    public function actionInfo()
    {
        $data = User::find()->where(['id' => Yii::$app->user->id])->one();
        return $this->_return($data);
    }
}	