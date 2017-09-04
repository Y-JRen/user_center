<?php
/**
 * Created by PhpStorm.
 * User: legend
 * Date: 2017/9/4
 * Time: 下午3:10
 */

namespace passport\modules\inside\models;


use yii\helpers\ArrayHelper;

class PreOrder extends \passport\models\PreOrder
{
    public function fields()
    {
        return [
            'order_id',
            'platform_order_id',
            'phone' => function ($model) {
                return ArrayHelper::getValue($model->user, 'phone');
            },
            'desc',
            'type',
            'amount',
            'status',
            'order_type',
            'created_at'
        ];
    }
}