<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\Platform */

$this->title = '更新平台: ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => '平台', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>

<div class="box box-info">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
