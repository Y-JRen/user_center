<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Coupons';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="coupon-index">

    <p>
        <?= Html::a('Create Coupon', ['create'], ['class' => 'btn btn-success']) ?>
    </p>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'platform_id',
            'dealer_id',
            'name',
            'short_name',
            // 'image',
            // 'type',
            // 'number',
            // 'amount',
            // 'effective_way',
            // 'start_time:datetime',
            // 'end_time:datetime',
            // 'upper_limit',
            // 'superposition',
            // 'tips',
            // 'desc',
            // 'status',
            // 'receive_start_time:datetime',
            // 'receive_end_time:datetime',
            // 'created_at',
            // 'updated_at',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
</div>
