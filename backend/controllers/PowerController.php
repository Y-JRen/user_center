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
    
    /**
     * @return array
     */
    public function actionNotify()
    {
        $strType = \Yii::$app->request->get('api');//测试可以手动拉取
        if(empty($strType))
        {
            $strType = \Yii::$app->request->post('api');//请求接口的时候告知变动类型是什么
        }
        
        $power = ThirdLogic::instance();
        switch($strType)
        {
            case 'projects/users':
                $power->getAdminUser();
                break;
            case 'projects/roles'://项目角色
                $power->getRoles();
                break;
            case 'projects/permission-tree'://项目菜单权限树形结构
                $power->getPermissionTree();//项目菜单
                break;
            //菜单信息变动
        }
        
        return ['message' => '更新成功'];
    }
}