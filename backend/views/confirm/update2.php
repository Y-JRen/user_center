<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\RechargeConfirm */

$this->title = 'Update Transfer Confirm: ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Transfer Confirms', 'url' => ['index2']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view2', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="transfer-confirm-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
