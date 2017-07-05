<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\Dealer */

$this->title = '更新经销商: ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => '经销商', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="box box-info">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
