<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "coupon_user".
 *
 * @property integer $id
 * @property integer $uid
 * @property integer $coupon_id
 * @property integer $start_valid_time
 * @property integer $end_valid_time
 * @property string $code
 * @property integer $status
 * @property integer $created_at
 * @property integer $updated_at
 */
class CouponUser extends BaseModel
{
    const STATUS_UNUSED = 10;
    const STATUS_USED = 20;
    const STATUS_EXPIRED = 30;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'coupon_user';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['uid', 'coupon_id', 'start_valid_time', 'end_valid_time', 'code', 'status', 'created_at', 'updated_at'], 'required'],
            [['uid', 'coupon_id', 'start_valid_time', 'end_valid_time', 'status', 'created_at', 'updated_at'], 'integer'],
            [['code'], 'string', 'max' => 12],
            [['code'], 'unique'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'uid' => '用户id',
            'coupon_id' => '卡券id',
            'start_valid_time' => '有效期起始时间',
            'end_valid_time' => '有效期截止时间',
            'code' => '券码',
            'status' => '状态;10未使用；20已使用；30已过期',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    public function behaviors()
    {
        return [TimestampBehavior::className()];
    }
}
