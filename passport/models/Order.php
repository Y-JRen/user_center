<?php
/**
 * Created by PhpStorm.
 * User: huawei
 * Date: 2017/7/13
 * Time: 9:51
 */

namespace passport\models;

use passport\helpers\Config;
use Yii;
use yii\helpers\ArrayHelper;

/**
 * Class Order
 * @package passport\models
 */
class Order extends \common\models\Order
{
    public function fields()
    {
        return [
            'platform_order_id',
            'order_id',
            'order_type',
            'order_subtype',
            'amount',
            'receipt_amount',
            'counter_fee',
            'discount_amount',
            'desc',
            'status',
            'statusName' => function ($model) {
                return $model->orderStatus;
            },
            'notice_platform_param',
            'platform' => function ($model) {
                return ArrayHelper::getValue(Config::getPlatformArray(), $model->platform);
            },
            'created_at' => function ($model) {
                return Yii::$app->formatter->asDatetime($model->created_at);
            },
            'updated_at' => function ($model) {
                return Yii::$app->formatter->asDatetime($model->created_at);
            }
        ];
    }


    // 交易记录默认搜索的状态
    public static $defaultSearchStatus = [
        self::STATUS_PROCESSING,
        self::STATUS_SUCCESSFUL,
        self::STATUS_FAILED,
        self::STATUS_PENDING,
        self::STATUS_TRANSFER,
    ];
}