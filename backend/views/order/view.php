<?php

use passport\helpers\Config;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\widgets\DetailView;
use backend\models\Order;
use common\helpers\JsonHelper;

/* @var $this yii\web\View */
/* @var $model backend\models\Order */

$this->title = $model->type . '订单:' . $model->order_id;
$this->params['breadcrumbs'][] = ['label' => '订单一览', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="order-view">
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'user.phone',
            'platform_order_id',
            'order_id',
            'type',
            'order_subtype',
            'amount:currency',
            'counter_fee:currency',
            'discount_amount:currency',
            'receipt_amount:currency',
            'orderStatus',
            'desc',
            'notice_status',
            'notice_platform_param',
            [
                'attribute' => 'platform',
                'value' => ArrayHelper::getValue(Config::$platformArray, $model->platform)
            ],
            [
                'attribute' => 'remark',
                'format' => 'raw',
                'value' => function ($model) {
                    $remarkArr = JsonHelper::BankHelper($model->remark);
                    $html = '';
                    foreach ($remarkArr as $key => $remark) {
                        if (!empty($value = ArrayHelper::getValue($remark, 'value'))) {
                            $label = ArrayHelper::getValue($remark, 'label');
                            if (is_array($value)) {
                                $link = '';
                                foreach ($value as $val) {
                                    $link .= Html::a('点击查看', $val, ['target' => '_blank']) . "&nbsp;";
                                }
                                $html .= Html::tag('p', "{$label}:{$link}");
                            } else {
                                $html .= Html::tag('p', "{$label}:{$value}");
                            }
                        }
                    }
                    return $html;
                }
            ],
            [
                'attribute' => 'remark',
                'value' => function ($model) {
                    return $model->order_subtype;
                }
            ],
            'created_at:datetime',
            'updated_at:datetime',
        ],
    ]) ?>
</div>