<?php

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
                    return '充值';
                }
            ],
            'order_subtype',
            'amount:currency',
            [
                'attribute' => 'status',
                'value' => function ($model) {
                    return '处理中';
                },
            ],
            'platform',
            'created_at:datetime',
            'updated_at:datetime',
            [
                'class' => 'yii\grid\ActionColumn', 'template' => '{view}',
                'buttons' =>
                    [
                        'view' => function ($url, $model, $key) {
                            return Html::a('<span class="glyphicon glyphicon-eye-open"></span>', ['view-line-down', 'id' => $model->id]);
                        },
                    ],
            ],
        ],
    ]); ?>
    <?php Pjax::end(); ?></div>
