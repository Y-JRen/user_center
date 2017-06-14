<?php

namespace passport\modules\sso\controllers;


use yii;
use passport\modules\sso\models\UserForm;

class UserController extends BaseController
{
    /**
     * 注册
     */
    public function actionReg()
    {
        $data['UserForm'] = yii::$app->request->post();
        $model = new UserForm();
        $model->setScenario($model::SCENARIO_REG);
        $model->load($data);
        if (!$model->validate()) {
            return $this->_error(1001, current($model->getFirstErrors()));
        }
        //注册
        $res = $model->reg();
        if (!$res['status']) {
            return $this->_error(1002, $res['msg']);
        }
        $token = $model->login($res['user_id']);
        return $this->_return(['token' => $token, 'uid' => $res['user_id']]);
    }

    /**
     * 登入
     */
    public function actionLogin()
    {
        $data['UserForm'] = yii::$app->request->post();
        $model = new UserForm();
        $model->setScenario($model::SCENARIO_LOGIN);
        $model->load($data);
        if (!$model->validate()) {
            return $this->_error(1001, current($model->getFirstErrors()));
        }
        //判断帐号密码
        $res = $model->checkLogin();
        if (!$res['status']) {
            return $this->_error(1003, $res['msg']);
        }
        $token = $model->login($res['user_id']);
        return $this->_return(['token' => $token, 'uid' => $res['user_id']]);
    }

    /**
     * 判断是否登入
     */
    public function actionCheckLogin()
    {
        $post = yii::$app->request->post();

        $data['UserForm'] = $post;
        $model = new UserForm();
        $model->setScenario($model::SCENARIO_LOGGED);
        $model->load($data);
        if (!$model->validate()) {
            return $this->_error(1005, current($model->getErrors())[0]);
        }
        $this->_return('已登录');
    }

    /**
     * 忘记密码
     */
    public function actionForgetPasswd()
    {
        $post = yii::$app->request->post();
        $data['UserForm'] = $post;
        $model = new UserForm();
        $model->setScenario($model::SCENARIO_REPASSWD);
        $model->load($data);
        if (!$model->validate()) {
            return $this->_error(1001, current($model->getErrors())[0]);
        }
        $res = $model->changePassword();
        if (!$res['status']) {
            return $this->_error(1006, $res['msg']);
        }
        return $this->_return('成功');
    }

    /**
     * 登出
     */
    public function actionLogout()
    {
        $post = yii::$app->request->post();

        $data['UserForm'] = $post;
        $model = new UserForm();
        $model->setScenario($model::SCENARIO_LOGGED);
        $model->load($data);
        if (!$model->validate()) {
            return $this->_error(1005, current($model->getErrors())[0]);
        }
        $model->logout();
        return $this->_return('成功');
    }
}
