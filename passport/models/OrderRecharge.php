<?php
/**
 * Created by PhpStorm.
 * User: legend
 * Date: 2017/8/11
 * Time: 下午3:33
 */

namespace passport\models;


use Yii;

class OrderRecharge extends Order
{
    public $openid;// 微信jssdk使用
    public $return_url; // 支付宝同步回调地址
    public $use;// 用途

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['uid', 'order_type', 'amount', 'order_subtype', 'platform'], 'required'],
            [['uid', 'order_type', 'status', 'notice_status', 'created_at', 'updated_at', 'quick_pay'], 'integer'],
            [['amount', 'receipt_amount', 'counter_fee', 'discount_amount'], 'number'],
            ['amount', 'compare', 'compareValue' => 0, 'operator' => '>'],
            [['platform_order_id', 'order_id'], 'string', 'max' => 30],
            [['order_subtype', 'desc', 'notice_platform_param'], 'string', 'max' => 255],
            ['order_id', 'unique'],
            ['order_subtype', 'in', 'range' => array_keys(self::$rechargeSubTypeName)],
            ['order_subtype', 'validatorOrderSubType'],
            [['openid', 'return_url', 'remark', 'use'], 'string'],
        ];
    }

    /**
     * 主要检测微信充值的必填参数
     */
    function validatorOrderSubType()
    {
        if ($this->order_subtype == self::SUB_TYPE_WECHAT_JSAPI && $this->isNewRecord) {
            if ($this->openid) {
                $this->remark = json_encode(['openid' => $this->openid]);
            } else {
                $this->addError('order_subtype', '参数有误');
                return false;
            }
        }
        return true;
    }

    public function beforeSave($insert)
    {
        if ($insert) {
            // 线下充值时，组合充值信息加入备注
            if ($this->order_type == self::TYPE_RECHARGE && $this->order_subtype == self::SUB_TYPE_LINE_DOWN && empty($this->remark)) {
                $remark = [];
                $keys = ['payType', 'transferDate', 'amount', 'referenceNumber', 'bankName', 'bankCard', 'accountName', 'referenceImg'];

                foreach ($keys as $key) {
                    if ($value = Yii::$app->request->post($key)) {
                        $remark[$key] = $value;
                    }
                }
                if (!empty($remark)) {
                    $this->remark = json_encode($remark);
                }
            }
        }
        return parent::beforeSave($insert);
    }
}