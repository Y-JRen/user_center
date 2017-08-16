<?php
/**
 * Created by PhpStorm.
 * User: legend
 * Date: 2017/8/15
 * Time: 下午2:25
 */

namespace console\controllers;


use passport\modules\pay\models\OrderClose;
use yii\console\Controller;

class OrderController extends Controller
{
    /**
     * 关闭30分钟前创建的待处理充值订单，线下充值的订单除外
     */
    public function actionClose()
    {
        /* @var $models OrderClose[] */
        $condition = 'order_type = :order_type and status = :status and order_subtype != :order_subtype and created_at < :created_at';
        $models = OrderClose::find()->where($condition, [
            ':order_type' => OrderClose::TYPE_RECHARGE,
            ':status' => OrderClose::STATUS_PENDING,
            ':order_subtype' => OrderClose::SUB_TYPE_LINE_DOWN,
            ':created_at' => time() - 60 * 30
        ])->all();

        $key = 0;
        foreach ($models as $key => $model) {
            $model->close();
        }
        $key++;

        echo "共计关闭：{$key}";

        return self::EXIT_CODE_NORMAL;
    }
}