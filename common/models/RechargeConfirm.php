<?php

namespace common\models;

use common\logic\FinanceLogic;
use Yii;

/**
 * This is the model class for table "recharge_confirm".
 *
 * @property integer $id
 * @property integer $order_id
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
            [['order_id', 'account_id', 'org_id', 'type_id', 'transaction_time', 'method', 'created_at'], 'integer'],
            [['remark'], 'string'],
            [['amount'], 'number'],
            ['order_id', 'unique'],
            [['account', 'back_order', 'org', 'type', 'att_ids'], 'string', 'max' => 255],
        ];
    }

    public function afterSave($insert, $changedAttributes)
    {
        $data = [
            'organization_id' => $this->org_id,
            'account_id' => $this->account_id,
            'tag_id' => $this->type_id,
            'money' => $this->amount,
            'time' => date('Y-m-d', $this->transaction_time),
            'trade_number' => $this->back_order,
        ];

        FinanceLogic::instance()->payment($data);
    }
}
