<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\Order */

$this->title = $model->order_id;
$this->params['breadcrumbs'][] = ['label' => 'Orders', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="order-view">


    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'uid',
            'platform_order_id',
            'order_id',
            'order_type',
            'order_subtype',
            'amount',
            'status',
            'desc',
            'notice_status',
            'notice_platform_param',
            'created_at',
            'updated_at',
            'remark',
            'platform',
        ],
    ]) ?>

</div>
