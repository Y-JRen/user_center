<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\CompanyAccount */

$this->title = '新建';
$this->params['breadcrumbs'][] = ['label' => 'Company Accounts', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="company-account-create">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
