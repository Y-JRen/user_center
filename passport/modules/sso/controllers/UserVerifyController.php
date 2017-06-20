<?php

namespace passport\modules\sso\controllers;


use passport\modules\sso\models\UserInfo;
use yii;
use passport\controllers\AuthController;

/**
 * 用户扩展信息接口
 *
 * Class UserVerifyController
 * @package passport\modules\sso\controllers
 */
class UserVerifyController extends AuthController
{
    public function verbs()
    {
        return [
            'verify' => ['post'],
            'info' => ['get']
        ];
    }

    /**
     * 实名认证
     *
     * @return array
     */
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

    /**
     * 获取用户扩展信息接口
     *
     * @return array|null|yii\db\ActiveRecord
     */
    public function actionInfo()
    {
        $data = UserInfo::find()->where(['uid' => Yii::$app->user->id])->one();
        return $this->_return($data);
    }
}	