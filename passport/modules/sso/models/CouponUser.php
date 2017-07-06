<?php
/**
 * Created by PhpStorm.
 * User: huawei
 * Date: 2017/7/6
 * Time: 15:42
 */

namespace passport\modules\sso\models;


use Yii;
use yii\helpers\ArrayHelper;

class CouponUser extends \common\models\CouponUser
{
    public $statusName;

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
     * 获取关联的优惠券
     * @return \yii\db\ActiveQuery
     */
    public function getCoupon()
    {
        return $this->hasOne(Coupon::className(), ['id' => 'coupon_id']);
    }

    /**
     * 生成券码
     * @return string
     */
    public function generateCode()
    {
        return Yii::$app->security->generateRandomString(12);
    }

    public function fields()
    {
        $data['name'] = function ($model) {
            return ArrayHelper::getValue($model->coupon, 'name');
        };
        $data['short_name'] = function ($model) {
            return ArrayHelper::getValue($model->coupon, 'short_name');
        };
        $data['image'] = function ($model) {
            return ArrayHelper::getValue($model->coupon, 'image');
        };
        $data['amount'] = function ($model) {
            return ArrayHelper::getValue($model->coupon, 'amount');
        };
        $data['tips'] = function ($model) {
            return ArrayHelper::getValue($model->coupon, 'tips');
        };
        $data['desc'] = function ($model) {
            return ArrayHelper::getValue($model->coupon, 'desc');
        };

        return ArrayHelper::merge($data, parent::fields());
    }
}