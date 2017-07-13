<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\Coupon */

$this->title = '创建卡券';
$this->params['breadcrumbs'][] = ['label' => '卡券', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="box box-info">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
