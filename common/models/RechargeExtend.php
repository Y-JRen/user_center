<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "recharge_extend".
 *
 * @property integer $order_id
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
            [['order_no', 'uid', 'use'], 'required'],
            [['uid'], 'integer'],
            [['order_no', 'use'], 'string', 'max' => 32],
            [['order_no'], 'unique'],
        ];
    }

}
