<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\RechargeConfirm */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Recharge Confirms', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="recharge-confirm-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('删除', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => '你确定要删除吗?',
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'order_id',
            'account_id',
            'account',
            'back_order',
            'org',
            'org_id',
            'type_id',
            'type',
            'transaction_time:datetime',
            'remark:ntext',
            'amount',
            'att_ids',
            'status',
            [
                'label' => '推送状态',
                'value'=> function ($model){
                    if($model->status==1){
                        return '推送';
                    }elseif($model->status==2){
                        return '不推送';
                    }elseif($model->status==3){
                        return '推送失败';
                    }elseif($model->status==4){
                        return '推送成功';
                    };
                }
            ],
            'method',
            'created_at',
        ],
    ]) ?>

</div>
