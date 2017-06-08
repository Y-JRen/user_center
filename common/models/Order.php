<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "order".
 *
 * @property integer $id
 * @property integer $uid
 * @property string $platform_order_id
 * @property string $order_id
 * @property integer $order_type
 * @property string $order_subtype
 * @property string $amount
 * @property integer $status
 * @property string $desc
 * @property integer $notice_status
 * @property string $notice_platform_param
 * @property integer $created_at
 * @property integer $updated_at
 * @property string $remark
 * @property integer $platform
 */
class Order extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'order';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['uid', 'platform_order_id', 'order_id', 'order_type', 'amount', 'status', 'created_at', 'updated_at'], 'required'],
            [['uid', 'order_type', 'status', 'notice_status', 'created_at', 'updated_at', 'platform'], 'integer'],
            [['amount'], 'number'],
            [['platform_order_id', 'order_id'], 'string', 'max' => 30],
            [['order_subtype', 'desc', 'notice_platform_param', 'remark'], 'string', 'max' => 255],
            [['order_id'], 'unique'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'uid' => 'Uid',
            'platform_order_id' => '平台订单号（che.com）',
            'order_id' => '订单号（用户中心）',
            'order_type' => '订单类型（充值、消费、提款、提现）',
            'order_subtype' => '支付方式，订单类型的子类型；如充值下面分：支付宝、微信、银行卡',
            'amount' => '金额',
            'status' => '状态',
            'desc' => '订单描述；类似订单标题',
            'notice_status' => '通知平台时返回的状态
1、未通知平台
2、通知平台失败
3、通知平台成功',
            'notice_platform_param' => '通知平台时所带参数',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'remark' => '备注',
            'platform' => '平台',
        ];
    }
}
