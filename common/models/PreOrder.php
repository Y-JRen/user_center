<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "pre_order".
 *
 * @property integer $id
 * @property integer $uid
 * @property string $platform_order_id
 * @property string $order_id
 * @property string $order_subtype
 * @property string $desc
 * @property double $amount
 * @property string $remark
 * @property integer $status
 * @property integer $platform
 * @property integer $quick_pay
 * @property integer $notice_status
 * @property string $notice_platform_param
 * @property integer $created_at
 * @property integer $updated_at
 */
class PreOrder extends BaseModel
{
    /**
     * 订单处理状态
     */
    const STATUS_SUCCESSFUL = Order::STATUS_SUCCESSFUL;// 处理成功
    const STATUS_PENDING = Order::STATUS_PENDING;// 待处理
    const STATUS_CLOSE = Order::STATUS_CLOSE;// 关闭

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'pre_order';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['uid', 'order_id', 'order_subtype', 'desc', 'amount', 'status', 'platform', 'notice_status'], 'required'],
            [['uid', 'status', 'platform', 'quick_pay', 'notice_status', 'created_at', 'updated_at'], 'integer'],
            [['amount'], 'number'],
            [['remark', 'order_subtype'], 'string'],
            [['platform_order_id', 'order_id'], 'string', 'max' => 32],
            [['desc', 'notice_platform_param'], 'string', 'max' => 255],
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
            'platform_order_id' => '平台订单号',
            'order_id' => '用户中心单号',
            'order_subtype' => '子类型',
            'desc' => '订单简述',
            'amount' => '订单金额',
            'remark' => '备注',
            'status' => '状态',
            'platform' => '平台来源',
            'quick_pay' => '快捷支付',//，完成后需要消费;0：不需要消费;1：需要消费
            'notice_status' => '通知状态',// ；1:需要通知;2:通知失败;3:通知成功;4:不需要通知
            'notice_platform_param' => '回调通知参数',
            'created_at' => '创建时间',
            'updated_at' => '最后更新时间',
        ];
    }

    /**
     * 关闭预处理订单
     */
    public function close()
    {
        $this->status = Order::STATUS_CLOSE;
        $this->save();
    }

    /**
     * 关联用户对象
     * @return \yii\db\ActiveQuery|User
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'uid']);
    }

    /**
     * 关联订单表
     * @return \yii\db\ActiveQuery|Order
     */
    public function getOrders()
    {
        return $this->hasMany(Order::className(), ['platform_order_id' => 'id']);
    }

    /**
     * 设置为成功
     * @return bool
     */
    public function setSuccess()
    {
        $this->status = self::STATUS_SUCCESSFUL;
        if ($this->save()) {
            return true;
        } else {
            Yii::error(var_export($this->errors, true), 'PreOrder');
            return false;
        }
    }

    /**
     * 设置为关闭
     * @return bool
     */
    public function setClose()
    {
        $this->status = self::STATUS_CLOSE;
        if ($this->save()) {
            return true;
        } else {
            Yii::error(var_export($this->errors, true), 'PreOrder');
            return false;
        }
    }

    public function behaviors()
    {
        return [
            TimestampBehavior::className(),
        ];
    }
}
