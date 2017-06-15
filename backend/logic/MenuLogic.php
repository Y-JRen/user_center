<?php
/**
 * Created by PhpStorm.
 * User: xiongjun
 * Date: 2017/6/15
 * Time: 15:38
 */

namespace backend\logic;


use common\fixtures\User;
use common\logic\Logic;
use common\models\AdminRole;
use yii\helpers\ArrayHelper;

class MenuLogic extends Logic
{
    /**
     * @var
     */
    public $roleId;
    
    /**
     * @return array
     */
    public function getTree()
    {
        $allMenu = ThirdLogic::instance()->getPermissionTree();
        return $this->getMenuList($allMenu);
    }
    
    private function getUserMenu()
    {
        $adminRole = AdminRole::findOne($this->roleId);
        if($adminRole) {
            $menu = json_decode($adminRole->permissions);
            return ArrayHelper::getColumn($menu, 'id');
        }
        return false;
    }
    
    /**
     * 菜单
     * @param $allMenu
     * @param array $items
     * @return array
     */
    private function getMenuList($allMenu, $items = [])
    {
        foreach ($allMenu as $k => $menu){
            if(!empty($this->getUserMenu()) && !in_array($menu['id'], $this->getUserMenu())) {
                continue;
            }
            $items[$k] = [
                'label' => $menu['name'],
                'url' => $menu['url'],
            ];
            if($menu['children']) {
                $child = $this->getMenuList($menu['children']);
                $items[$k]['items'] = $child;
            }
        }
        return $items;
    }
}