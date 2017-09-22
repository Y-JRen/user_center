<?php
/**
 * Created by PhpStorm.
 * User: huawei
 * Date: 2017/6/12
 * Time: 14:44
 */

namespace backend\models;


use common\models\PoolBalance;
use common\models\User;
use common\models\UserInfo;
use yii\helpers\ArrayHelper;
use Yii;

/**
 * Class Order
 */
class Order extends \common\models\Order
{
    const SCENARIO_FINANCE_CONFIRM = 'finance_confirm';// 财务确认 线下充值确认、提现确认
    const SCENARIO_WRITE_OFF = 'write_off';// 核销

    public $phone;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        $defaultRules = [
            [['uid', 'order_id', 'order_type', 'amount', 'status'], 'required'],
            [['uid', 'order_type', 'status', 'notice_status', 'created_at', 'updated_at', 'platform', 'quick_pay'], 'integer'],
            [['amount'], 'number'],
            [['platform_order_id', 'order_id'], 'string', 'max' => 30],
            [['order_subtype', 'desc', 'notice_platform_param'], 'string', 'max' => 255],
            [['order_id'], 'unique'],
            [['remark'], 'string'],
        ];

        if ($this->scenario == self::SCENARIO_WRITE_OFF) {
            return ArrayHelper::merge($defaultRules, self::$writeOffRules);
        } else {
            return $defaultRules;
        }
    }

    public static $writeOffRules = [
        [['remark', 'phone', 'desc'], 'required'],
        ['order_type', 'in', 'range' => [self::TYPE_REFUND, self::TYPE_CONSUME]],
    ];


    public function scenarios()
    {
        return [
            self::SCENARIO_DEFAULT => [],
            self::SCENARIO_FINANCE_CONFIRM => ['remark', 'status', 'updated_at'],
            self::SCENARIO_WRITE_OFF => ['phone', 'order_subtype', 'desc', 'remark', 'amount', 'order_type'],
        ];
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

    //充值状态
    public static $rechargeStatusArray = [
        self::STATUS_SUCCESSFUL => '充值成功',
        self::STATUS_FAILED => '充值失败',
        self::STATUS_PENDING => '待处理',
    ];

    //提现和审批状态
    public static $cashStatusArray = [
        self::STATUS_PROCESSING => '提现申请中',
        self::STATUS_SUCCESSFUL => '审批通过',
        self::STATUS_FAILED => '审批不通过',
        self::STATUS_TRANSFER => '出纳已打款',
    ];

    /**
     * 设置订单状态为已打款
     * @return bool
     */
    public function setOrderTransfer()
    {
        $this->status = self::STATUS_TRANSFER;
        return $this->save();
    }

    /**
     * 支付方式格式化
     * @return mixed
     */
    public static $subTypeName = [
        self::SUB_TYPE_CONSUME_QUICK_PAY => '快捷支付',
        self::SUB_TYPE_CONSUME_FEE => '手续费',
        self::SUB_TYPE_LOAN_RECORD => '贷款入账',
        self::SUB_TYPE_LOAN_REFUND => '贷款出账',
        'wechat_code' => '微信二维码',
        'wechat_jsapi' => '公众号支付',
        'alipay_pc' => '支付宝PC网站支付',
        'alipay_wap' => '支付宝手机网站支付',
        'alipay_app' => '支付宝APP支付',
        'alipay_mobile' => '支付宝移动支付',
        'line_down' => '线下充值',
        'bank' => '银行',
        'lakala' => '拉卡拉POS机',
        self::SUB_TYPE_TMALL => '天猫',
        'ddsbtk' => '订单失败退款',
        self::SUB_TYPE_WRITE_OFF => '核销',
    ];

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'uid' => '手机号',
            'phone' => '手机号',
            'platform_order_id' => '平台订单号',
            'order_id' => '会员中心单号',
            'order_type' => '交易类型',
            'type' => '交易类型',
            'order_subtype' => '交易方式',
            'amount' => '金额',
            'status' => '状态',
            'orderStatus' => '状态',
            'desc' => '订单描述',
            'notice_status' => '通知平台状态',
            'notice_platform_param' => '通知平台时所带参数',
            'created_at' => '创建时间',
            'updated_at' => '处理时间',
            'remark' => '备注',
            'platform' => '平台',
            'quick_pay' => '快捷支付',
            'receipt_amount' => '实际交易金额',
            'counter_fee' => '服务费',
            'discount_amount' => '优惠金额',
        ];
    }

    /**
     * 添加财务失败的操作日志
     *
     * @param $remark
     * @return array
     */
    public function addLogReview($remark = '')
    {
        $result = ['status' => true, 'info' => ''];
        $model = new LogReview();
        $model->order_id = $this->id;
        $model->order_status = $this->status;
        $model->remark = $remark;
        if (!$model->save()) {
            $result['status'] = false;
            $result['info'] = current($model->getFirstErrors());
            Yii::error(var_export($model->errors, true));
        }

        return $result;
    }

    /**
     * 获取提现审批用户
     * @return string
     */
    public function getCashUser()
    {
        if ($this->order_type == self::TYPE_CASH && in_array($this->status, [self::STATUS_SUCCESSFUL, self::STATUS_FAILED, self::STATUS_TRANSFER])) {
            $model = LogReview::find()->where(['order_id' => $this->id, 'order_status' => self::STATUS_SUCCESSFUL])->one();
            if (isset($model->admin)) {
                return ArrayHelper::getValue($model->admin, 'name');
            }
        }
        return '';
    }

    /**
     * 获取订单用户扩展信息
     * @return \yii\db\ActiveQuery
     */
    public function getUserInfo()
    {
        return $this->hasOne(UserInfo::className(), ['uid' => 'uid']);
    }

    /**
     * 获取平账订单资金记录
     * 只适用于核销订单相关
     *
     * @return \yii\db\ActiveQuery|PoolBalance
     */
    public function getPoolBalance()
    {
        return $this->hasOne(PoolBalance::className(), ['order_id' => 'order_id']);
    }
}