<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\RechargeConfirm */

$this->title = 'Create Recharge Confirm';
$this->params['breadcrumbs'][] = ['label' => 'Recharge Confirms', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="recharge-confirm-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
