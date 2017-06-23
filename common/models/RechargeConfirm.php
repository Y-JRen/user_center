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

    public function getOrder()
    {
        return $this->hasOne(Order::className(), ['order_id' => 'order_id']);
    }

    public function afterSave($insert, $changedAttributes)
    {
        if ($insert) {
            if ($this->status == 1) {
                $remark = ArrayHelper::getValue($this->order, 'remark');
                $remarkArr = JsonHelper::BankHelper($remark);
                $data = [
                    'organization_id' => $this->org_id,
                    'account_id' => $this->account_id,
                    'tag_id' => $this->type_id,
                    'money' => $this->amount,
                    'time' => date('Y-m-d', $this->transaction_time),
                    'trade_number' => $this->back_order,
                    'order_number' => $this->order_id,
                    'other_name' => ArrayHelper::getValue($remarkArr, 'accountName'),
                    'other_card' => ArrayHelper::getValue($remarkArr, 'bankCard'),
                    'other_bank' => ArrayHelper::getValue($remarkArr, 'bankName'),
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
