<?php
/**
 * Created by PhpStorm.
 * User: xiongjun
 * Date: 2017/6/12
 * Time: 18:16
 */

namespace passport\modules\sso\controllers;


use passport\controllers\AuthController;
use passport\logic\AccountLogic;

/**
 * 客户账号相关
 *
 * Class AccountController
 * @package passport\modules\sso\controllers
 */
class AccountController extends AuthController
{
    /**
     * 客户账号
     *
     * @return array
     */
    public function actionIndex()
    {
        $userId = \Yii::$app->user->identity->getId();
        $data = AccountLogic::instance()->accountList($userId);
        return $this->_return($data);
    }
}