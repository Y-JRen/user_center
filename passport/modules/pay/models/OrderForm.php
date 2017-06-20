<?php
/**
 * Created by PhpStorm.
 * User: xiongjun
 * Date: 2017/6/6
 * Time: 09:43
 */

namespace passport\modules\pay\models;

use common\jobs\OrderCallbackJob;
use common\logic\RefundLogin;
use passport\logic\AccountLogic;
use Yii;
use common\models\Order;
use passport\helpers\Config;
use yii\behaviors\TimestampBehavior;
use yii\db\Exception;

/**
 * 订单表单
 *
 * Class OrderForm
 *
 */
class OrderForm extends Order
{
    public $openid;// 微信jssdk使用
    public $return_url; // 支付宝同步回调地址
    public $finance_id;// 退款记录ID必填 退款专用

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['uid', 'order_type', 'amount'], 'required'],
            [['uid', 'order_type', 'status', 'notice_status', 'created_at', 'updated_at', 'quick_pay'], 'integer'],
            [['amount'], 'number'],
            [['platform_order_id', 'order_id'], 'string', 'max' => 30],
            [['order_subtype', 'desc', 'notice_platform_param', 'remark'], 'string', 'max' => 255],
            ['order_id', 'unique'],
            ['order_type', 'in', 'range' => [self::TYPE_RECHARGE, self::TYPE_CONSUME, self::TYPE_REFUND, self::TYPE_CASH]],
            ['order_subtype', 'in', 'range' => ['wechat_code', 'wechat_jsapi', 'alipay_pc', 'alipay_wap', 'alipay_app', 'alipay_mobile', 'line_down'], 'when' => function ($model) {
                return $model->order_type == self::TYPE_RECHARGE;
            }],
            ['order_subtype', 'validatorOrderSubType'],
            [['openid', 'return_url'], 'string'],
            ['finance_id', 'integer']
        ];
    }

    /**
     * 主要检测微信充值的必填参数
     */
    function validatorOrderSubType()
    {
        if ($this->order_type == self::TYPE_RECHARGE && $this->order_subtype == 'wechat_jsapi') {
            if ($this->openid) {
                $this->remark = json_encode(['openid' => $this->openid]);
            } else {
                $this->addError('order_subtype', '参数有误');
                return false;
            }
        }
        return true;
    }

    /**
     * @param bool $insert
     * @return bool
     */
    public function beforeSave($insert)
    {
        // 新增订单时，设置平台、订单号、初始状态
        if ($this->isNewRecord) {
            $this->quick_pay = (empty($this->quick_pay) ? 0 : $this->quick_pay);
            // 生成快捷消费订单时不需要设置一下两个信息
            if (!($this->quick_pay && $this->order_type == self::TYPE_CONSUME)) {
                $this->uid = Yii::$app->user->id;
                $this->platform = Config::getPlatform();
            }
            $this->order_id = Config::createOrderId();
            $this->status = self::STATUS_PENDING;
        }
        return parent::beforeSave($insert);
    }

    /**
     *  消费订单
     *  1、消费时需要冻结金额
     *  2、冻结完之后再解冻
     */
    public function consumeSave()
    {
        $this->consumeFreeze();
        return $this->consumeUnfreeze();
    }

    /**
     * 退款订单
     */
    public function refundSave()
    {
        $transaction = Yii::$app->db->beginTransaction();
        try {

            if (!$this->checkRefundStatus()) {
                throw new Exception('该笔订单，erp不允许退款');
            }

            if (!$this->refundCheck()) {
                throw new Exception('该笔订单，已有退款记录');
            }

            if (!$this->userBalance->plus($this->amount)) {
                throw new Exception('余额增加失败');
            }

            if (!$this->setOrderSuccess()) {
                throw new Exception('更新消费订单状态失败');
            }

            $transaction->commit();
            return true;
        } catch (Exception $e) {
            $transaction->rollBack();
            $this->setOrderFail();
            throw $e;
        }
    }

    /**
     * 提现
     */
    public function cashSave()
    {
        //将银行卡信息保存起来，正确错误与否不重要
        AccountLogic::instance()->addAccount(json_decode($this->remark, true));

        $transaction = Yii::$app->db->beginTransaction();
        try {
            if (!$this->userBalance->less($this->amount)) {
                throw new Exception('余额扣除失败');
            }

            if (!$this->userFreeze->plus($this->amount)) {
                throw new Exception('冻结失败');
            }

            if (!$this->setOrderProcessing()) {
                throw new Exception('更新订单状态失败');
            }

            $transaction->commit();
            return true;
        } catch (Exception $e) {
            $transaction->rollBack();
            throw $e;
        }
    }

    /**
     * 添加一条快捷支付的消费订单
     *
     * 如果消费失败，通知平台，到余额去支付
     */
    public function addQuickPayOrder()
    {
        $model = new self;
        $model->uid = $this->uid;
        $model->platform_order_id = $this->platform_order_id;
        $model->order_id = Config::createOrderId();
        $model->order_type = self::TYPE_CONSUME;
        $model->order_subtype = self::SUB_TYPE_CONSUME_QUICK_PAY;
        $model->amount = $this->amount;
        $model->status = self::STATUS_PROCESSING;
        $model->desc = '快捷支付消费订单';
        $model->notice_status = 1;
        $model->notice_platform_param = $this->notice_platform_param;
        $model->remark = $this->remark;
        $model->platform = $this->platform;
        $model->quick_pay = $this->quick_pay;

        if ($model->save()) {
            $model->consumeSave();
            return true;
        } else {
            return false;
        }
    }

    /**
     * 检测erp是否允许退款
     *
     * @return bool
     */
    public function checkRefundStatus()
    {
        $data = [
            'amount' => $this->amount,
            'financeId' => intval($this->finance_id),
            'onlineSaleNo' => $this->platform_order_id
        ];
        return RefundLogin::instance()->orderConfirm($data);
    }

    /**
     * 查看该电商订单号是否有过退款操作
     * @return bool
     */
    public function refundCheck()
    {
        return Order::find()->where([
                'platform_order_id' => $this->platform_order_id,
                'status' => self::STATUS_SUCCESSFUL,
            ]
        )->exists();
    }

    /**
     * 消费冻结
     */
    protected function consumeFreeze()
    {
        $transaction = Yii::$app->db->beginTransaction();
        try {
            if (!$this->userBalance->less($this->amount)) {
                throw new Exception('余额扣除失败');
            }

            if (!$this->userFreeze->plus($this->amount)) {
                throw new Exception('资金冻结失败');
            }

            if (!$this->setOrderProcessing()) {
                throw new Exception('更新消费订单状态失败');
            }
            $transaction->commit();
            return true;
        } catch (Exception $e) {
            $transaction->rollBack();
            throw $e;
        }
    }

    /**
     * 消费解冻
     */
    protected function consumeUnfreeze()
    {
        $model = self::find()->where(['id' => $this->id])->one();// 对象缓存，导致解冻失败
        $transaction = Yii::$app->db->beginTransaction();
        try {
            if (!$model->userFreeze->less($this->amount)) {
                throw new Exception('资金解冻失败');
            }

            if (!$this->setOrderSuccess()) {
                throw new Exception('更新消费订单状态失败');
            }

            $transaction->commit();

            // 异步回调通知平台, 快捷消费订单不在此处回调
            if (!$this->quick_pay) {
                Yii::$app->queue_second->push(new OrderCallbackJob([
                    'notice_platform_param' => $this->notice_platform_param,
                    'order_id' => $this->order_id,
                    'platform_order_id' => $this->platform_order_id,
                    'quick_pay' => $this->quick_pay,
                    'status' => 1,
                ]));
            }
            return true;
        } catch (Exception $e) {
            $transaction->rollBack();

            // 异步回调通知平台, 快捷消费订单不在此处回调
            if (!$this->quick_pay) {
                Yii::$app->queue_second->push(new OrderCallbackJob([
                    'notice_platform_param' => $this->notice_platform_param,
                    'order_id' => $this->order_id,
                    'platform_order_id' => $this->platform_order_id,
                    'quick_pay' => $this->quick_pay,
                    'status' => 2,
                ]));
            }
            throw $e;
        }
    }
}