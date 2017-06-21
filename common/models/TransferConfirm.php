<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "transfer_confirm".
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
 * @property integer $created_at
 */
class TransferConfirm extends BaseModel
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'transfer_confirm';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['order_id', 'account_id', 'account', 'back_order', 'org', 'org_id', 'type_id', 'type', 'transaction_time', 'created_at'], 'required'],
            [['order_id', 'account_id', 'org_id', 'type_id', 'transaction_time', 'created_at'], 'integer'],
            [['remark'], 'string'],
            ['order_id', 'unique'],
            [['account', 'back_order', 'org', 'type'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'order_id' => 'Order ID',
            'account_id' => 'Account ID',
            'account' => 'Account',
            'back_order' => 'Back Order',
            'org' => 'Org',
            'org_id' => 'Org ID',
            'type_id' => 'Type ID',
            'type' => 'Type',
            'transaction_time' => 'Transaction Time',
            'remark' => 'Remark',
            'created_at' => 'Created At',
        ];
    }
}
