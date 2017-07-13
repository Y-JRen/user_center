<?php

use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '用户领取详情';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="coupon-index">
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'user.phone',
            'start_valid_time:datetime',
            'end_valid_time:datetime',
            'code',
            [
                'label' => '状态',
                'attribute' => 'status',
                'value' => function ($model) {
                    return \yii\helpers\ArrayHelper::getValue(\backend\models\CouponUser::$statusArr, $model->status);
                }
            ],
            'created_at:datetime'
        ],
    ]); ?>
</div>
