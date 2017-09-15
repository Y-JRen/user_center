<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "recharge_extend".
 *
 * @property integer $id
 * @property string $object_name
 * @property integer $object_id
 * @property string $order_no
 * @property integer $uid
 * @property string $use
 */
class RechargeExtend extends BaseModel
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'recharge_extend';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['object_name', 'object_id', 'order_no', 'uid', 'use'], 'required'],
            [['object_id', 'uid'], 'integer'],
            [['object_name'], 'string', 'max' => 255],
            [['order_no', 'use'], 'string', 'max' => 32],
            [['order_no'], 'unique'],
        ];
    }

    /**
     * 获取关联的订单
     * @return \yii\db\ActiveQuery|Order|PreOrder
     */
    public function getOrder()
    {
        return $this->hasOne($this->object_name, ['id' => 'object_id']);
    }
}
