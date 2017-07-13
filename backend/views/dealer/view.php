<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\Dealer */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => '经销商', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="dealer-view">

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
            [
                'attribute' => 'platform.name_cn',
            ],
            'platform_dealer_id',
            'name',
        ],
    ]) ?>

</div>
