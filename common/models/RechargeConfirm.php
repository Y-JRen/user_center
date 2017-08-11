<?php

namespace common\models;

use common\helpers\JsonHelper;
use common\logic\FinanceLogic;
use Yii;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "recharge_confirm".
 *
 * @property integer $id
 * @property string $order_id
 * @property integer $account_id
 * @property string $account
 * @property string $back_order
 * @property string $org
 * @property integer $org_id
 * @property integer $type_id
 * @property string $type
 * @property integer $transaction_time
 * @property string $remark
 * @property string $amount
 * @property string $att_ids
 * @property string $method
 * @property integer $status
 * @property integer $created_at
 */
class RechargeConfirm extends BaseModel
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'recharge_confirm';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['order_id', 'account_id', 'account', 'back_order', 'org', 'org_id', 'type_id', 'type', 'transaction_time', 'created_at'], 'required'],
            [['account_id', 'org_id', 'type_id', 'transaction_time', 'method', 'status', 'created_at'], 'integer'],
            [['remark'], 'string'],
            [['amount'], 'number'],
            ['order_id', 'unique'],
            [['order_id', 'account', 'back_order', 'org', 'type', 'att_ids'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'order_id' => '订单号',
            'account_id' => '公司收款账号id',
            'account' => '收款账号',
            'back_order' => '银行流水号',
            'org' => '机构',
            'org_id' => '机构id',
            'type_id' => '类型id',
            'type' => '类型',
            'transaction_time' => '到账时间',
            'remark' => '标记',
            'amount' => '金额',
            'att_ids' => '附件id',
            'status' => '推送状态',
            'method' => '支付方式',
            'created_at' => '创建时间',
        ];
    }

    public function getOrder()
    {
        return $this->hasOne(Order::className(), ['order_id' => 'order_id']);
    }

    public function afterSave($insert, $changedAttributes)
    {
        if ($insert) {
            if ($this->status == 1) {
                $remark = ArrayHelper::getValue($this->order, 'remark');
                $subType = ArrayHelper::getValue($this->order, 'order_subtype');
                $quickPay = ArrayHelper::getValue($this->order, 'quick_pay');

                if (empty($this->remark) && $subType) {
                    $this->remark = ArrayHelper::getValue(Order::$rechargeSubTypeName, $subType) . '方式';
                    $this->remark .= '的' . ($quickPay ? '快捷支付' : '用户充值');
                }

                $remarkArr = JsonHelper::BankHelper($remark);
                $data = [
                    'organization_id' => $this->org_id,
                    'account_id' => $this->account_id,
                    'tag_id' => $this->type_id,
                    'money' => $this->amount,
                    'time' => date('Y-m-d', $this->transaction_time),
                    'trade_number' => $this->back_order,
                    'order_number' => $this->order_id,
                    'other_name' => ArrayHelper::getValue(ArrayHelper::getValue($remarkArr, 'accountName', []), 'value'),
                    'other_card' => ArrayHelper::getValue(ArrayHelper::getValue($remarkArr, 'bankCard', []), 'value'),
                    'other_bank' => ArrayHelper::getValue(ArrayHelper::getValue($remarkArr, 'bankName', []), 'value'),
                    'remark' => $this->remark,
                    'order_type' => 104
                ];

                $result = FinanceLogic::instance()->payment($data);
                if ($result['success']) {
                    $this->status = 3;
                } else {
                    $this->status = 4;
                }
                $this->save();
            }
        }
    }
}
