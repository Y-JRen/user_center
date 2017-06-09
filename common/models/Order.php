<?php

namespace common\models;

use passport\helpers\Config;
use Yii;
use yii\behaviors\TimestampBehavior;

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
 * @property integer $quick_pay
 *
 * @property UserBalance $userBalance
 * @property UserFreeze $userFreeze
 */
class Order extends \yii\db\ActiveRecord
{
    /**
     * 订单处理类型
     */
    const TYPE_RECHARGE = 1;// 充值
    const TYPE_CONSUME = 2;// 消费
    const TYPE_REFUND = 3;// 退款
    const TYPE_CASH = 4;// 提现

    /**
     * 订单处理状态
     */
    const STATUS_PROCESSING = 1;// 处理中
    const STATUS_SUCCESSFUL = 2;// 处理成功
    const STATUS_FAILED = 3;// 处理失败

    /**
     * 消费子类型
     */
    const SUB_TYPE_CONSUME_QUICK_PAY = 'quick_pay';// 快捷支付识别字符

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
            [['uid', 'order_type', 'status', 'notice_status', 'created_at', 'updated_at', 'platform', 'quick_pay'], 'integer'],
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
            'order_type' => '订单类型',
            'order_subtype' => '支付方式',
            'amount' => '金额',
            'status' => '状态',
            'desc' => '订单描述；类似订单标题',
            'notice_status' => '通知平台状态',
            'notice_platform_param' => '通知平台时所带参数',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'remark' => '备注',
            'platform' => '平台',
            'quick_pay' => '快捷支付'
        ];
    }

    public function fields()
    {
        return [
            'platform_order_id',
            'order_id',
            'order_type',
            'order_subtype',
            'amount' => function ($model) {
                return Yii::$app->formatter->asCurrency($model->amount);
            },
            'desc',
            'notice_platform_param',
            'created_at' => function ($model) {
                return Yii::$app->formatter->asDatetime($model->created_at);
            },
            'updated_at' => function ($model) {
                return Yii::$app->formatter->asDatetime($model->created_at);
            }
        ];
    }

    /**
     * 获取用户余额
     * @return \yii\db\ActiveQuery | UserBalance
     */
    public function getBalance()
    {
        return $this->hasOne(UserBalance::className(), ['uid' => 'uid']);
    }

    /**
     * 获取当前关联的冻结金额对象
     * @return mixed|UserFreeze
     */
    public function getUserBalance()
    {
        $object = $this->balance;
        if (empty($object)) {
            $object = new UserBalance();
            $object->uid = $this->uid;
            $object->amount = 0;
            $object->updated_at = time();
        }
        return $object;
    }

    /**
     * 获取用户冻结余额
     * @return \yii\db\ActiveQuery | UserFreeze
     */
    public function getFreeze()
    {
        return $this->hasOne(UserFreeze::className(), ['uid' => 'uid']);
    }

    /**
     * 获取当前关联的冻结金额对象
     * @return mixed|UserFreeze
     */
    public function getUserFreeze()
    {
        $object = $this->freeze;
        if (empty($object)) {
            $object = new UserFreeze();
            $object->uid = $this->uid;
            $object->amount = 0;
            $object->updated_at = time();
        }
        return $object;
    }


    public function behaviors()
    {
        return [
            TimestampBehavior::className(),
        ];
    }

    /**
     * 设置订单状态为成功
     * @return bool
     */
    public function setOrderSuccess()
    {
        $this->status = self::STATUS_SUCCESSFUL;
        return $this->save();
    }

    /**
     * 设置订单状态为失败
     * @return bool
     */
    public function setOrderFail()
    {
        $this->status = self::STATUS_FAILED;
        return $this->save();
    }


}
