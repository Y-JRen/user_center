<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\User */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Users', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-view">

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'phone',
            'user_name',
            'email:email',
            'passwd',
            'status',
            'from_platform',
            'from_channel',
            'reg_time:datetime',
            'reg_ip',
            'login_time:datetime',
        ],
    ]) ?>

</div>
