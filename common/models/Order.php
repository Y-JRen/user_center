<?php

namespace common\models;

use common\traits\FreezeTrait;
use Exception;
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
class Order extends BaseModel
{
    use FreezeTrait;
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
    const STATUS_PENDING = 4;// 待处理
    const STATUS_TRANSFER = 5;// 出纳已转账
    const STATUS_CLOSE = 6;// 关闭；允许第三方充值异步回调

    public static $statusArray = [
        self::STATUS_PROCESSING => '处理中',
        self::STATUS_PROCESSING => '处理成功',
        self::STATUS_PROCESSING => '处理不成功',
        self::STATUS_PROCESSING => '待处理',
        self::STATUS_PROCESSING => '已打款',
        self::STATUS_PROCESSING => '已关闭',
    ];


    /**
     * 充值子类型
     */
    const SUB_TYPE_WECHAT_CODE = 'wechat_code';
    const SUB_TYPE_WECHAT_JSAPI = 'wechat_jsapi';
    const SUB_TYPE_WECHAT_APP = 'wechat_app';
    const SUB_TYPE_ALIPAY_PC = 'alipay_pc';
    const SUB_TYPE_ALIPAY_WAP = 'alipay_wap';
    const SUB_TYPE_ALIPAY_APP = 'alipay_app';
    const SUB_TYPE_ALIPAY_MOBILE = 'alipay_mobile';
    const SUB_TYPE_LINE_DOWN = 'line_down';
    const SUB_TYPE_LAKALA = 'lakala';
    const SUB_TYPE_TMALL = 'tmall';

    /**
     * 充值中英文对照
     * @var array
     */
    public static $rechargeSubTypeName = [
        self::SUB_TYPE_WECHAT_CODE => '微信二维码',
        self::SUB_TYPE_WECHAT_JSAPI => '微信公众号',
        self::SUB_TYPE_WECHAT_APP => '微信APP',
        self::SUB_TYPE_ALIPAY_PC => '支付宝PC网站',
        self::SUB_TYPE_ALIPAY_WAP => '支付宝手机网站',
        self::SUB_TYPE_ALIPAY_APP => '支付宝APP',
        self::SUB_TYPE_ALIPAY_MOBILE => '支付宝移动',
        self::SUB_TYPE_LINE_DOWN => '线下',
        self::SUB_TYPE_LAKALA => '拉卡拉POS机',
        self::SUB_TYPE_TMALL => '天猫',
    ];

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
            [['uid', 'order_id', 'order_type', 'amount', 'status'], 'required'],
            [['uid', 'order_type', 'status', 'notice_status', 'created_at', 'updated_at', 'platform', 'quick_pay'], 'integer'],
            [['amount', 'receipt_amount', 'counter_fee', 'discount_amount'], 'number'],
            [['platform_order_id', 'order_id'], 'string', 'max' => 30],
            [['order_subtype', 'desc', 'notice_platform_param'], 'string', 'max' => 255],
            [['order_id'], 'unique'],
            [['remark'], 'string'],
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
            'amount' => '订单金额',
            'status' => '状态',
            'desc' => '订单描述',
            'notice_status' => '通知平台状态',
            'notice_platform_param' => '通知平台时所带参数',
            'created_at' => '创建时间',
            'updated_at' => '最后一次更新时间',
            'remark' => '备注',
            'platform' => '平台',
            'quick_pay' => '快捷支付',
            'receipt_amount' => '实际金额',
            'counter_fee' => '服务费',
            'discount_amount' => '优惠金额',
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
     * 关闭订单
     * @return bool
     */
    public function setOrderClose()
    {
        $this->status = self::STATUS_CLOSE;
        if ($this->save()) {
            return true;
        } else {
            Yii::error(var_export($this->errors, true), 'orderClose');
            return false;
        }
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
     * 获取订单所有状态名称
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
            self::STATUS_TRANSFER => '已打款',
            self::STATUS_CLOSE => '已关闭',
        ];

        return is_null($key) ? $data : ArrayHelper::getValue($data, $key);
    }

    public static $rechargeStatusArray = [
        self::STATUS_PROCESSING => '充值中',
        self::STATUS_SUCCESSFUL => '充值成功',
        self::STATUS_FAILED => '充值失败',
        self::STATUS_PENDING => '待处理',
        self::STATUS_CLOSE => '已关闭',
    ];

    public static $consumeStatusArray = [
        self::STATUS_PROCESSING => '消费中',
        self::STATUS_SUCCESSFUL => '消费成功',
        self::STATUS_FAILED => '消费失败',
        self::STATUS_PENDING => '待处理',
    ];

    public static $refundStatusArray = [
        self::STATUS_PROCESSING => '退款处理中',
        self::STATUS_SUCCESSFUL => '退款成功',
        self::STATUS_FAILED => '退款失败',
        self::STATUS_PENDING => '待处理',
    ];

    public static $cashStatusArray = [
        self::STATUS_PROCESSING => '提现申请中',
        self::STATUS_SUCCESSFUL => '提现成功',
        self::STATUS_FAILED => '提现失败',
        self::STATUS_PENDING => '待处理',
        self::STATUS_TRANSFER => '提现成功',
    ];

    /**
     * 获取订单状态名称
     * 对象方法
     *
     * @return array|mixed
     */
    public function getOrderStatus()
    {

        switch ($this->order_type) {
            case self::TYPE_RECHARGE:
                $array = static::$rechargeStatusArray;
                break;
            case self::TYPE_CONSUME:
                $array = self::$consumeStatusArray;
                break;
            case self::TYPE_REFUND:
                $array = self::$refundStatusArray;
                break;
            case self::TYPE_CASH:
                $array = static::$cashStatusArray;
                break;
            default:
                $array = self::getStatusName();
        }
        return ArrayHelper::getValue($array, $this->status);
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
        $model->after_amount = ((float)$model->before_amount + (float)$model->amount);
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
        $model->after_amount = ((float)$model->before_amount + (float)$model->amount);
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

    /**
     * 获取该订单的微信支付配置
     * @return array
     */
    public function getWeChatConfig()
    {
        return Config::getWeChatConfig($this->order_subtype);
    }

    /**
     * 获取充值订单的扩展数据
     * @return \yii\db\ActiveQuery|RechargeExtend
     */
    public function getRechargeExtend()
    {
        return $this->hasOne(RechargeExtend::className(), ['order_on' => 'order_id']);
    }

    public function beforeSave($insert)
    {
        if ($this->isNewRecord) {
            // 实际金额处理
            if (empty($this->receipt_amount)) {
                $this->receipt_amount = (float)$this->amount + (float)$this->counter_fee - (float)$this->discount_amount;
            }
        }
        return parent::beforeSave($insert);
    }

    public function afterSave($insert, $changedAttributes)
    {
        // 充值成功后的一些处理
        if ($this->order_type == self::TYPE_RECHARGE) {
            if ((ArrayHelper::getValue($changedAttributes, 'status', 0) == self::STATUS_PENDING) && ($this->status == self::STATUS_SUCCESSFUL)) {
                if ($this->rechargeExtend) {
                    // 当前订单用户是备用金
                    if ($this->rechargeExtend->use == 'intention_gold') {
                        $transaction = Yii::$app->db->beginTransaction();
                        try {
                            $this->createFreeze($this);
                            $transaction->commit();
                        } catch (Exception $e) {
                            $transaction->rollBack();
                            Yii::error($e->getMessage(), 'order_after_save');
                        }
                    }
                }
            }
        }
    }
}
