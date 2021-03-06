<?php
/**
 * Created by PhpStorm.
 * User: xiongjun
 * Date: 2017/6/15
 * Time: 10:31
 */

namespace backend\logic;


use common\logic\HttpLogic;
use passport\logic\Logic;
use yii\db\Exception;
use yii\helpers\ArrayHelper;

/**
 * 系统权限第三方接口
 *
 *
 * Class ThirdLogic
 * @package passport\modules\sso\logic
 */
class ThirdLogic extends Logic
{
    public $baseUrl;

    public $_token;

    public function init()
    {
        $this->baseUrl = \Yii::$app->params['power']['apiUrl'];
        $this->_token = \Yii::$app->params['power']['token'];
    }

    /**
     * 获取用户
     */
    public function getAdminUser()
    {
        $url = 'projects/users';
        $arrPost = [
            '_token' => $this->_token,
            'per_page' => 100000,
            'show_deleted' => 1,
        ];
        $data = [];
        $returnData = HttpLogic::instance()->http($this->baseUrl . $url, 'POST', $arrPost);
        $returnData = json_decode($returnData, true);
        if ($returnData['success'] == 1 && is_array($returnData['data']) && !empty($returnData['data'])
            && !empty($returnData['data']['data'])
        ) {
            foreach ($returnData['data']['data'] as $val) {
                $data[] = [
                    $val['id'],
                    $val['name'],
                    $val['organization_id'],
                    $val['status'] == 1 ? 0 : 1,
                    $val['position_name'],
                    $val['email'],
                    $val['phone'],
                    $val['bqq_open_id'],
                    implode(',', ArrayHelper::getColumn($val['roles'], 'id'))
                ];
            }
        }
        $db = \Yii::$app->db;
        $transaction = $db->beginTransaction();
        try {
            $db->createCommand()->delete('admin_user')->execute();
            $db->createCommand()->batchInsert('admin_user', [
                'id', 'name', 'org_id', 'is_delete', 'profession', 'email', 'phone', 'bqq_open_id', 'role_ids'
            ], $data)->execute();
            $transaction->commit();
            return true;
        } catch (Exception $e) {
            $transaction->rollBack();
            throw $e;
        }
    }

    /**
     * 获取菜单树形结构
     *
     * @return mixed
     */
    public function getPermissionTree()
    {
        $url = 'projects/permission-tree';
        $arrPost = [
            '_token' => $this->_token,
            'per_page' => 100000,
        ];
        $returnData = json_decode(HttpLogic::instance(['debug' => 0])->http($this->baseUrl . $url, 'POST', $arrPost), true);
        if ($returnData['success'] == 1 && is_array($returnData['data']) && !empty($returnData['data'])
            && !empty($returnData['data'])
        ) {
            return $returnData['data'];
        }
        return [];
    }

    /**
     * 获取 角色权限
     *
     * @return array|bool
     * @throws Exception
     */
    public function getRoles()
    {
        $url = 'projects/roles';
        $arrPost = [
            '_token' => $this->_token,
        ];
        $returnData = json_decode(HttpLogic::instance(['debug' => 0])->http($this->baseUrl . $url, 'POST', $arrPost), true);
        if ($returnData['success'] == 1 && is_array($returnData['data']) && !empty($returnData['data'])
            && !empty($returnData['data'])
        ) {
            $data = [];
            foreach ($returnData['data']['data'] as $val) {
                $data[] = [
                    $val['id'],
                    $val['name'],
                    $val['slug'],
                    json_encode($val['permissions']),
                ];
            }
            if (!empty($data)) {
                $db = \Yii::$app->db;
                $transaction = $db->beginTransaction();
                try {
                    $db->createCommand()->delete('admin_role')->execute();
                    $db->createCommand()->batchInsert('admin_role', [
                        'id', 'name', 'slug', 'permissions'
                    ], $data)->execute();
                    $transaction->commit();
                    return true;
                } catch (Exception $e) {
                    $transaction->rollBack();
                    throw $e;
                }
            }
        }
        return [];
    }

    /**
     * 获取菜单树形结构
     *
     * @return mixed
     */
    public function getPermission()
    {
        $url = 'projects/permissions';
        $arrPost = [
            '_token' => $this->_token,
            'per_page' => 100000,
        ];
        $returnData = json_decode(HttpLogic::instance(['debug' => 0])->http($this->baseUrl . $url, 'POST', $arrPost), true);
        if ($returnData['success'] == 1 && is_array($returnData['data']) && !empty($returnData['data'])
            && !empty($returnData['data'])
        ) {
            return $returnData['data']['data'];
        }
        return [];
    }

    /**
     * 远程获取用户的项目，更新缓存
     * @param $id
     */
    public function getRemoteUserProjects($id)
    {
        $url = "users/{$id}/projects";
        $arrGet = [
            '_token' => $this->_token,
        ];

        $requestUrl = $this->baseUrl . $url . '?' . http_build_query($arrGet);

        $returnData = json_decode(HttpLogic::instance()->http($requestUrl, 'GET'), true);
        if ($returnData['success'] == 1 && is_array($returnData['data']) && !empty($returnData['data'])) {
            $redis = \Yii::$app->redis;
            $redis->set(self::getUserProjectsRedisKey($id), json_encode($returnData['data']));
            $redis->expire(self::getUserProjectsRedisKey($id), 86400);
        }
    }

    public function getUserProjects($id)
    {
        $redis = \Yii::$app->redis;
        $data = json_decode($redis->get(self::getUserProjectsRedisKey($id)), true);
        if (is_array($data)) {
            return $data;
        } else {
            return [];
        }
    }

    public static function getUserProjectsRedisKey($id)
    {
        return 'UserProjects' . $id;
    }
}