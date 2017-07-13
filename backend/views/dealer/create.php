<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\Dealer */

$this->title = '添加经销商';
$this->params['breadcrumbs'][] = ['label' => '经销商', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="box box-info">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
