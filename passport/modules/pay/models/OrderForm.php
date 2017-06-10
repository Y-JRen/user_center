<?php
/**
 * Created by PhpStorm.
 * User: xiongjun
 * Date: 2017/6/6
 * Time: 09:43
 */

namespace passport\modules\pay\models;


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

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['uid', 'order_type', 'amount'], 'required'],
            [['uid', 'order_type', 'status', 'notice_status', 'created_at', 'updated_at'], 'integer'],
            [['amount'], 'number'],
            [['platform_order_id', 'order_id'], 'string', 'max' => 30],
            [['order_subtype', 'desc', 'notice_platform_param', 'remark'], 'string', 'max' => 255],
            ['order_id', 'unique'],
            ['order_type', 'in', 'range' => [self::TYPE_RECHARGE, self::TYPE_CONSUME, self::TYPE_REFUND, self::TYPE_CASH]],
            ['order_subtype', 'in', 'range' => ['wechat_code', 'wechat_jsapi', 'alipay_pc', 'alipay_wap', 'line_down'], 'when' => function ($model) {
                return $model->order_type == self::TYPE_RECHARGE;
            }],
            ['order_subtype', 'validatorOrderSubType'],
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
            $this->platform = Config::getPlatform();
            $this->order_id = Config::createOrderId();
            $this->status = self::STATUS_PROCESSING;
        }
        return parent::beforeSave($insert);
    }

    /**
     *  消费订单
     *
     * @return bool
     * @throws Exception
     */
    public function consumeSave()
    {
        $transaction = \Yii::$app->db->beginTransaction();
        try {
            if (!$this->userBalance->less($this->amount)) {
                throw new Exception('余额扣除失败');
            }

            if ($this->setOrderSuccess()) {
                $transaction->commit();
                return true;
            } else {
                throw new Exception('更新消费订单状态失败');
            }
        } catch (Exception $e) {
            $transaction->rollBack();
            throw $e;
        }
    }

    /**
     * 提现
     */
    public function cashSave()
    {
        $transaction = \Yii::$app->db->beginTransaction();
        try {
            if (!$this->userBalance->less($this->amount)) {
                throw new Exception('余额扣除失败');
            }

            if (!$this->userFreeze->plus($this->amount)) {
                throw new Exception('冻结失败');
            }

            $transaction->commit();
            return true;
        } catch (Exception $e) {
            $transaction->rollBack();
            throw $e;
        }
    }
}