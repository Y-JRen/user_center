<?php

namespace backend\controllers;

use backend\models\LogReview;
use backend\models\Order;
use Yii;
use yii\helpers\ArrayHelper;


class LogReviewController extends BaseController
{
    /**
     * 获取失败订单的原因
     *
     * @param $orderId int
     * @return string
     */
    public function actionFail($orderId)
    {
        $model = LogReview::find()->where(['order_id' => $orderId, 'order_status' => Order::STATUS_FAILED])->orderBy('id desc')->one();

        if ($model) {
            $admin = ArrayHelper::getValue($model->admin, 'name', '未知的用户');
            $time = Yii::$app->formatter->asDatetime($model->created_at);
            $html = "<p>{$admin}审批不通过（{$time}）</p>";
            $html .= "<p class='hint-block'>{$model->remark}</p>";
        } else {
            $html = "<p>该审批没有记录</p>";
        }

        return $html;
    }
}
