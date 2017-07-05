<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\Platform */

$this->title = '添加平台';
$this->params['breadcrumbs'][] = ['label' => '平台', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="box box-info">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
