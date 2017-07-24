<?php
/**
 * Created by PhpStorm.
 * User: xiongjun
 * Date: 2017/6/15
 * Time: 15:38
 */

namespace backend\logic;


use common\logic\Logic;
use common\models\AdminRole;
use yii\helpers\ArrayHelper;

/**
 * 后台菜单
 *
 * Class MenuLogic
 * @package backend\logic
 */
class MenuLogic extends Logic
{
    /**
     * @var
     */
    public $roleId;

    /**
     * 获取所有菜单
     *
     * @return array
     */
    public function getTree()
    {
        $allMenu = ThirdLogic::instance()->getPermissionTree();
        return $this->getMenuList($allMenu);
    }

    /**
     * 获取用户菜单
     *
     * @return array|bool
     */
    private function getUserMenu()
    {
        $adminRole = AdminRole::findOne($this->roleId);
        if ($adminRole) {
            $menu = json_decode($adminRole->permissions);
            return ArrayHelper::getColumn($menu, 'id');
        }
        return false;
    }

    /**
     * 菜单（左侧菜单）
     *
     * @param $allMenu
     * @param array $items
     * @return array
     */
    private function getMenuList($allMenu, $items = [])
    {
        foreach ($allMenu as $k => $menu) {
            if (!empty($this->getUserMenu()) && !in_array($menu['id'], $this->getUserMenu())) {
                continue;
            }
            $items[$k] = [
                'label' => $menu['name'],
                'url' => [$menu['url']],
                'icon' => $menu['slug'],
                'active' => is_null($menu['parent_id']),
            ];
            if ($menu['children']) {
                $child = $this->getMenuList($menu['children']);
                $items[$k]['items'] = $child;
            }
        }


        return $items;
    }
}