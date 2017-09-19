<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "freeze_record".
 *
 * @property integer $id
 * @property string $order_no
 * @property integer $uid
 * @property string $use
 * @property double $amount
 * @property integer $status
 * @property integer $created_at
 * @property integer $updated_at
 *
 * @property User $user
 */
class FreezeRecord extends BaseModel
{
    const STATUS_FREEZE_OK = 2;// 已冻结
    const STATUS_THAW_OK = 3;// 解冻成功
    const STATUS_THAW_FAIL = 4;// 解冻失败

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'freeze_record';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['order_no', 'uid', 'use', 'amount', 'status'], 'required'],
            [['uid', 'status', 'created_at', 'updated_at'], 'integer'],
            [['amount'], 'number'],
            [['order_no', 'use'], 'string', 'max' => 32],
            [['order_no'], 'unique'],
        ];
    }

    /**
     * 获取用户信息
     * @return \yii\db\ActiveQuery|User
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'uid']);
    }

    /**
     * 获取订单信息
     * @return \yii\db\ActiveQuery|Order
     */
    public function getOrder()
    {
        return $this->hasOne(Order::className(), ['order_id' => 'order_no']);
    }

    public function behaviors()
    {
        return [
            TimestampBehavior::className(),
        ];
    }
}
