<?php
/**
 * Created by PhpStorm.
 * User: huawei
 * Date: 2017/6/22
 * Time: 17:59
 */

namespace common\logic;

use common\models\User;
use yii\helpers\ArrayHelper;


/**
 * crm 系统的相关接口
 * Class CrmLogic
 * @package common\logic
 */
class CrmLogic extends Logic
{

    /**
     * 通过用户id，获取该客户属于哪个销售，哪个组织，财务系统对接需要
     * @param $uid
     * @return array|bool
     */
    public function getOrgInfo($uid)
    {
        $user = User::find()->where(['id' => $uid])->asArray()->one();
        $phone = ArrayHelper::getValue($user, 'phone');
        if (empty($phone)) {
            return false;
        } else {
            $url = \Yii::$app->params['crm']['domain'] . '/thirdpartyapi/clue/get-clue?phone=' . $phone;
            $resultInfo = HttpLogic::instance()->http($url, 'GET');
            $result = json_decode($resultInfo, true);
            if ($result['msg'] == 'success') {
                return $result['data'];
            }
        }
        return false;
    }
}