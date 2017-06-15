<?php
/**
 * Created by PhpStorm.
 * User: xiongjun
 * Date: 2017/6/15
 * Time: 10:24
 */

namespace backend\controllers;


use backend\logic\ThirdLogic;
use yii\rest\Controller;

/**
 * 权限系统接口
 *
 *
 * Class ThirdPowerController
 * @package passport\modules\sso\controllers
 */
class PowerController extends Controller
{
    /**
     * 获取所有用户
     */
    public function actionAllUser()
    {
        $rst = ThirdLogic::instance()->getAdminUser();
        if ($rst) {
            return ['message' => '用户更新成功'];
        } else  {
            return [];
        }
    }
    
    /**
     * 获取所有菜单
     */
    public function actionMenu()
    {
        $rst = ThirdLogic::instance()->getPermissionTree();
        var_dump($rst);die;
    }
    
    /**
     * 获取所有菜单
     */
    public function actionRole()
    {
        $rst = ThirdLogic::instance()->getRoles();
        if ($rst) {
            return ['message' => '角色更新成功'];
        } else  {
            return [];
        }
    }
}