<?php
/**
 * Created by PhpStorm.
 * User: huawei
 * Date: 2017/7/6
 * Time: 15:42
 */

namespace passport\modules\sso\models;


use Yii;

class CouponUser extends \common\models\CouponUser
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['uid', 'coupon_id', 'start_valid_time', 'end_valid_time', 'code', 'status'], 'required'],
            [['uid', 'coupon_id', 'start_valid_time', 'end_valid_time', 'status', 'created_at', 'updated_at'], 'integer'],
            [['code'], 'string', 'max' => 12],
            [['code'], 'unique'],
        ];
    }

    /**
     * 生成券码
     * @return string
     */
    public function generateCode()
    {
        return Yii::$app->security->generateRandomString(12);
    }
}