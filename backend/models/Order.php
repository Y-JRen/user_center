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

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['uid', 'order_id', 'order_type', 'amount', 'status'], 'required'],
            [['uid', 'order_type', 'status', 'notice_status', 'created_at', 'updated_at', 'platform', 'quick_pay'], 'integer'],
            [['amount'], 'number'],
            [['platform_order_id', 'order_id'], 'string', 'max' => 30],
            [['order_subtype', 'desc', 'notice_platform_param', 'remark'], 'string', 'max' => 255],
            [['order_id'], 'unique'],
        ];
    }

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
            self::STATUS_PENDING => '待处理',
            self::STATUS_TRANSFER => '已转账',
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


    /**
     * 判断财务是否可以确认提现
     * @return bool
     */
    public function getFinanceConfirmCash()
    {
        return ($this->order_type == self::TYPE_CASH && $this->status == self::STATUS_PROCESSING);
    }

    /**
     * 设置订单状态为已打款
     * @return bool
     */
    public function setOrderTransfer()
    {
        $this->status = self::STATUS_TRANSFER;
        return $this->save();
    }
}