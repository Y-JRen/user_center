<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "recharge_confirm".
 *
 * @property integer $id
 * @property integer $order_id
 * @property integer $account_id
 * @property string $account
 * @property string $back_order
 * @property integer $created_at
 */
class RechargeConfirm extends \yii\db\ActiveRecord
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
            [['order_id', 'account_id', 'account', 'back_order', 'created_at'], 'required'],
            [['order_id', 'account_id', 'created_at'], 'integer'],
            ['order_id', 'unique'],
            [['account', 'back_order'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'order_id' => '充值订单表的主键id',
            'account_id' => '收款账号id',
            'account' => '收款账户',
            'back_order' => '银行流水号',
            'created_at' => 'Created At',
        ];
    }
}
