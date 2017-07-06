<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\Coupon */

$this->title = '更新卡券: ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => '卡券', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['详情', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="box box-info">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
