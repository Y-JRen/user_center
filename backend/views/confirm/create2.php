<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\RechargeConfirm */

$this->title = 'Create Transfer Confirm';
$this->params['breadcrumbs'][] = ['label' => 'Transfer Confirms', 'url' => ['index2']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="recharge-confirm-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
