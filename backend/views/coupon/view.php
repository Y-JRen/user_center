<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\Coupon */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Coupons', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="coupon-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'platform_id',
            'dealer_id',
            'name',
            'short_name',
            'image',
            'type',
            'number',
            'amount',
            'effective_way',
            'start_time',
            'end_time',
            'upper_limit',
            'superposition',
            'tips',
            'desc',
            'status',
            'receive_start_time',
            'receive_end_time',
            'created_at:datetime',
            'updated_at:datetime',
        ],
    ]) ?>

</div>
