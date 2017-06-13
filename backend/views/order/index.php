<?php

use common\models\Order;
use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\search\OrderrSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '消费记录';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="order-index">

    <?php Pjax::begin(['enablePushState' => false]); ?>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => isset($searchModel) ? $searchModel : null,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            [
                'attribute' => 'uid',
                'label' => '用户',
                'value' => function ($model) {
                    return \common\models\User::findOne($model->uid)->phone;
                }
            ],
            'platform_order_id',
            'order_id',
            [
                'attribute' => 'order_type',
                'value' => function ($model) {
                    return $model->type;
                },
                'filter' => Order::getTypeName()
            ],
            'order_subtype',
            [
                'attribute' => 'amount',
                'value' => function ($model) {
                    return Yii::$app->formatter->asCurrency($model->amount);
                }
            ],
            [
                'attribute' => 'status',
                'value' => function ($model) {
                    return $model->orderStatus;
                },
                'filter' => Order::getStatusName()
            ],
            'created_at:datetime',
            'updated_at:datetime',
            'platform',
            [
                'class' => 'yii\grid\ActionColumn', 'template' => '{view}',
                'buttons' =>
                    [
                        'view' => function ($url, $model, $key) {
                            $actionUrl = 'view';
                            if ($model->isEdit) {
                                if ($model->order_type == Order::TYPE_RECHARGE && $model->order_subtype == 'line_down') {
                                    $actionUrl = 'view-line-down';
                                } elseif ($model->order_type == Order::TYPE_CASH) {
                                    $actionUrl = 'view-cash';
                                }
                            }
                            return Html::a('<span class="glyphicon glyphicon-eye-open"></span>', [$actionUrl, 'id' => $model->id]);
                        },
                    ],
            ],
        ],
    ]); ?>
    <?php Pjax::end(); ?></div>
