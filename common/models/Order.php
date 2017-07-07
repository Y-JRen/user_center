<?php

namespace common\models;

use passport\helpers\Config;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\helpers\ArrayHelper;

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
 * @property string $receipt_amount
 * @property string $counter_fee
 * @property string $discount_amount
 *
 * @property string $type
 * @property string $orderStatus
 *
 * @property UserBalance $userBalance
 * @property UserFreeze $userFreeze
 * @property bool $isSuccessful
 * @property bool $isEdit
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
    const STATUS_PROCESSING = 1;// 带处理
    const STATUS_SUCCESSFUL = 2;// 处理成功
    const STATUS_FAILED = 3;// 处理失败
    const STATUS_PENDING = 4;// 待处理
    const STATUS_TRANSFER = 5;// 出纳已转账

    /**
     * 消费子类型
     */
    const SUB_TYPE_CONSUME_QUICK_PAY = 'quick_pay';// 快捷支付识别字符
    const SUB_TYPE_CONSUME_FEE = 'fee';// 手续费消费
    const SUB_TYPE_LOAN_RECORD = 'loan_record';// 贷款入账 充值、消费时都使用
    const SUB_TYPE_LOAN_REFUND = 'loan_refund';// 贷款退款

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
            [['amount', 'receipt_amount', 'counter_fee', 'discount_amount'], 'number'],
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
            'platform_order_id' => '平台订单号',
            'order_id' => '订单号',
            'order_type' => '订单类型',
            'type' => '订单类型',
            'order_subtype' => '支付方式',
            'amount' => '金额',
            'status' => '状态',
            'desc' => '订单描述',
            'notice_status' => '通知平台状态',
            'notice_platform_param' => '通知平台时所带参数',
            'created_at' => '创建时间',
            'updated_at' => '最后一次更新时间',
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
            'amount',
            'desc',
            'status',
            'statusName' => function ($model) {
                return $this->orderStatus;
            },
            'notice_platform_param',
            'platform' => function ($model) {
                return ArrayHelper::getValue(Config::getPlatformArray(), $model->platform);
            },
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
        unset($this->balance);
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
        unset($this->freeze);
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
     * 设置订单状态为处理中
     * @return bool
     */
    public function setOrderProcessing()
    {
        $this->status = self::STATUS_PROCESSING;
        return $this->save();
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

    /**
     * 判断订单是否处理成功
     * @return bool
     */
    public function getIsSuccessful()
    {
        return $this->status == self::STATUS_SUCCESSFUL;
    }

    /**
     * 判断财务是否可以审核
     * @return bool
     */
    public function getIsEdit()
    {
        return $this->status == self::STATUS_PROCESSING;
    }

    /**
     * 获取订单类型
     * 静态方法
     *
     * @param null $key
     * @return array|mixed
     */
    public static function getTypeName($key = null)
    {
        $data = [
            self::TYPE_RECHARGE => '充值',
            self::TYPE_CONSUME => '消费',
            self::TYPE_REFUND => '退款',
            self::TYPE_CASH => '提现',
        ];

        return is_null($key) ? $data : ArrayHelper::getValue($data, $key);
    }

    /**
     * 获取订单类型名称
     * 对象方法
     *
     * @return array|mixed
     */
    public function getType()
    {
        return self::getTypeName($this->order_type);
    }

    /**
     * 获取订单状态名称
     * 静态方法
     *
     * @param $key
     * @return array|mixed
     */
    public static function getStatusName($key = null)
    {
        $data = [
            self::STATUS_PROCESSING => '处理中',
            self::STATUS_SUCCESSFUL => '处理成功',
            self::STATUS_FAILED => '处理不成功',
            self::STATUS_PENDING => '待处理',
        ];

        return is_null($key) ? $data : ArrayHelper::getValue($data, $key);
    }

    /**
     * 获取订单状态名称
     * 对象方法
     *
     * @return array|mixed
     */
    public function getOrderStatus()
    {
        return self::getStatusName($this->status);
    }

    /**
     * 异常订单处理
     */
    public function exceptionHandle()
    {
        Yii::$app->params['orderErr'] = [
            'order_id' => $this->order_id,
            'platform_order_id' => $this->platform_order_id,
            'notice_platform_param' => $this->notice_platform_param
        ];

//        Yii::error(json_encode($this->errors));
    }

    /**
     * 添加用户余额资金流水记录
     * @param $style string 流水方式
     * @return bool
     */
    public function addPoolBalance($style)
    {
        if ($style == PoolBalance::STYLE_PLUS) {
            $amount = $this->receipt_amount;
        } elseif ($style == PoolBalance::STYLE_LESS) {
            $amount = -$this->receipt_amount;
        } else {
            return false;
        }

        $model = new PoolBalance();
        $model->created_at = time();
        $model->order_id = $this->order_id;
        $model->amount = $amount;
        $model->before_amount = PoolBalance::getUserBalance($this->uid);
        $model->after_amount = ($model->before_amount + $model->amount);
        $model->uid = $this->uid;
        $model->desc = $this->getDescription();
        if ($model->save()) {
            return true;
        } else {
            Yii::error(json_encode($model->errors), 'modelSave');
            return false;
        }
    }

    /**
     * 添加用户余额资金流水记录
     * @param $style string 流水方式
     * @return bool
     */
    public function addPoolFreeze($style)
    {
        if ($style == PoolFreeze::STYLE_PLUS) {
            $amount = $this->receipt_amount;
        } elseif ($style == PoolBalance::STYLE_LESS) {
            $amount = -$this->receipt_amount;
        } else {
            return false;
        }

        $model = new PoolFreeze();
        $model->created_at = time();
        $model->order_id = $this->order_id;
        $model->amount = $amount;
        $model->before_amount = PoolFreeze::getUserBalance($this->uid);
        $model->after_amount = ($model->before_amount + $model->amount);
        $model->uid = $this->uid;
        $model->desc = $this->getDescription();
        if ($model->save()) {
            return true;
        } else {
            Yii::error(json_encode($model->errors), 'modelSave');
            return false;
        }
    }

    /**
     * 先放4大类型，后期扩展
     *
     */
    public function getDescription()
    {
        return $this->getType();
    }
}
