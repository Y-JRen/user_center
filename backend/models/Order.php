<?php
/**
 * Created by PhpStorm.
 * User: huawei
 * Date: 2017/6/12
 * Time: 14:44
 */

namespace backend\models;


use common\models\User;
use yii\helpers\ArrayHelper;

class Order extends \common\models\Order
{
    const SCENARIO_FINANCE_CONFIRM = 'finance_confirm';// 财务确认 线下充值确认、提现确认

    public function scenarios()
    {
        return [
            self::SCENARIO_DEFAULT => [],
            self::SCENARIO_FINANCE_CONFIRM => ['remark', 'status', 'updated_at'],
        ];
    }

    /**
     * 获取订单状态
     * @param null $key
     * @return array|mixed
     */
    public static function getStatus($key = null)
    {
        $data = [
            self::STATUS_PROCESSING => '处理中',
            self::STATUS_SUCCESSFUL => '处理通过',
            self::STATUS_FAILED => '处理不通过',
            self::STATUS_FAILED => '待处理',
        ];

        if (is_null($key)) {
            return $data;
        } else {
            return ArrayHelper::getValue($data, $key);
        }
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'uid']);
    }

}