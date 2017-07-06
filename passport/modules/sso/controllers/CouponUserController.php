<?php
/**
 * Created by PhpStorm.
 * User: huawei
 * Date: 2017/7/6
 * Time: 15:31
 */

namespace passport\modules\sso\controllers;

use passport\modules\sso\models\Coupon;
use passport\modules\sso\models\CouponUser;
use Yii;
use passport\controllers\AuthController;
use yii\base\InvalidParamException;

class CouponUserController extends AuthController
{
    /**
     * 用户领取卡券
     */
    public function actionGet()
    {
        $currentTime = time();
        $couponId = Yii::$app->request->get('coupon_id');
        $coupon = $this->findModel($couponId);

        // 检测活动是否结束
        if ($coupon->status == Coupon::STATUS_END) {
            return $this->_return('该卡券领取活动已结束');
        }

        // 检测卡券领取时间是否正确
        if ($currentTime < $coupon->receive_start_time) {
            return $this->_return('领取时间未到');
        }

        if ($currentTime >= $coupon->receive_end_time) {
            return $this->_return('领取时间已过');
        }

        // 库存数量
        $count = CouponUser::find()->where(['coupon_id' => $couponId])->count();
        if ($count >= $coupon->number) {
            return $this->_return('已全部领取完毕');
        }

        // 用户领取的数量
        $userCount = CouponUser::find()->where(['coupon_id' => $couponId, 'uid' => Yii::$app->user->id])->count();
        if ($userCount >= $coupon->upper_limit) {
            return $this->_return('您已达到领取的上限');
        }

        // 发放卡券给用户
        $model = new CouponUser();
        $model->uid = Yii::$app->user->id;
        $model->coupon_id = $coupon->id;
        if ($coupon->effective_way == Coupon::EFFECTIVE_WAY_FIXED) {
            $model->start_valid_time = $coupon->start_time;
            $model->end_valid_time = $coupon->end_time;
        } else {
            $model->start_valid_time = ($currentTime + $coupon->start_time * 86400);
            $model->end_valid_time = ($model->start_valid_time + $coupon->end_time * 86400);
        }
        $model->code = $model->generateCode();
        $model->status = CouponUser::STATUS_UNUSED;
        if ($model->save()) {
            return $this->_return($model);
        } else {
            return $this->_return('领取失败了，再试一次、、、');
        }
    }

    /**
     * @param $id
     * @return array|null|Coupon
     */
    protected function findModel($id)
    {
        $model = Coupon::find()->where(['id' => $id])->one();
        if ($model !== null) {
            return $model;
        } else {
            throw new InvalidParamException('传递参数有误', 1101);
        }
    }
}