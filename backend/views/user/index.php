<?php

use passport\helpers\Config;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\search\UserSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '用户管理';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-index">


    <?php Pjax::begin(); ?>    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            [
                'attribute' => 'phone',
                'format' => 'raw',
                'value' => function ($model) {
                    return Html::a($model->phone, ['/user/order', 'uid' => $model->id]);
                }
            ],
            'user_name',
            'email:email',
            'status',
            [
                'attribute' => 'from_platform',
                'value' => function ($model) {
                    return ArrayHelper::getValue(Config::getPlatformArray(), $model->from_platform);
                },
            ],
            'from_channel',
            'reg_time:datetime',
            'reg_ip',
            'login_time:datetime',

            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '{view} {order_view} {amount_view}',
                'buttons' => [
                    'view' => function ($url, $model, $key) {
                        return Html::a('详情', ['user/view', 'id' => $model->id], ['class' => 'btn btn-success btn-xs']);
                    },
                    'order_view' => function ($url, $model, $key) {
                        return Html::a('订单详情', ['user/order', 'uid' => $model->id], ['class' => 'btn btn-success btn-xs']);
                    },
                    'amount_view' => function ($url, $model, $key) {
                        return Html::a('资金详情', ['user/amount', 'uid' => $model->id], ['class' => 'btn btn-success btn-xs']);
                    }
                ]
            ],
        ],
    ]); ?>
    <?php Pjax::end(); ?></div>
