<?php

use common\models\Order;
use passport\helpers\Config;
use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\search\OrderSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '提现审核记录';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="order-index">

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => isset($searchModel) ? $searchModel : null,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            [
                'attribute' => 'uid',
                'label' => '用户',
                'format' => 'raw',
                'value' => function ($model) {
                    $phone = \common\models\User::findOne($model->uid)->phone;
                    return Html::a($phone, ['/user/order', 'uid' => $model->uid]);
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
            [
                'attribute' => 'platform',
                'value' => function ($model) {
                    return \yii\helpers\ArrayHelper::getValue(Config::getPlatformArray(), $model->platform);
                },
            ],
            [
                'class' => 'yii\grid\ActionColumn', 'template' => '{view}',
                'buttons' => [
                    'view' => function ($url, $model, $key) {
                        return Html::a('<span class="glyphicon glyphicon-eye-open"></span>', ['/cash/confirm', 'id'=>$model->id]);
                    }
                ]
            ],
        ],
    ]); ?>
</div>