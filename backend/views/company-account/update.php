<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\CompanyAccount */

$this->title = '公司账号更新管理: ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Company Accounts', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="company-account-update">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
