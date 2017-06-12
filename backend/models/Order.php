<?php
/**
 * Created by PhpStorm.
 * User: huawei
 * Date: 2017/6/12
 * Time: 14:44
 */

namespace backend\models;


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
            self::STATUS_PROCESSING => '待处理',
            self::STATUS_SUCCESSFUL => '处理通过',
            self::STATUS_FAILED => '处理不通过',
        ];

        if (is_null($key)) {
            return $data;
        } else {
            return ArrayHelper::getValue($data, $key);
        }
    }

}