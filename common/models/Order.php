<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "order".
 *
 * @property integer $id
 * @property integer $uid
 * @property string $platform_order_id
 * @property string $order_id
 * @property integer $order_type
 * @property string $order_subtype
 * @property double $amount
 * @property integer $status
 * @property string $desc
 * @property integer $notice_status
 * @property string $notice_platform_param
 * @property integer $created_at
 * @property integer $updated_at
 * @property string $remark
 */
class Order extends BaseModel
{
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
            [['uid', 'platform_order_id', 'order_id', 'order_type', 'amount', 'status', 'created_at', 'updated_at'], 'required'],
            [['uid', 'order_type', 'status', 'notice_status', 'created_at', 'updated_at'], 'integer'],
            [['amount'], 'number'],
            [['platform_order_id', 'order_id'], 'string', 'max' => 30],
            [['order_subtype', 'desc', 'notice_platform_param', 'remark'], 'string', 'max' => 255],
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
            'platform_order_id' => 'Platform Order ID',
            'order_id' => 'Order ID',
            'order_type' => 'Order Type',
            'order_subtype' => 'Order Subtype',
            'amount' => 'Amount',
            'status' => 'Status',
            'desc' => 'Desc',
            'notice_status' => 'Notice Status',
            'notice_platform_param' => 'Notice Platform Param',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'remark' => 'Remark',
        ];
    }
}
