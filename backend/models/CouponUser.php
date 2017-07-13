<?php

namespace backend\models;

use common\models\User;
use Yii;

class CouponUser extends \common\models\CouponUser
{
    public static $statusArr = [
        self::STATUS_UNUSED => '未使用',
        self::STATUS_USED => '已使用',
        self::STATUS_EXPIRED => '已过期',
    ];

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'uid']);
    }
}
