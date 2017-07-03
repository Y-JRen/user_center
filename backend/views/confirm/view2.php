<?php
use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\RechargeConfirm */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Transfer Confirms', 'url' => ['index2']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="transfer-confirm-view">

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
//            'transaction_time:datetime',
            [
                'attribute' => 'transaction_time',
                'label' => '到账时间',
                'value' => function($model){
                    return date('Y-m-d H:i:s',$model->transaction_time);
                }
            ],
            'remark:ntext',
            'amount',
            'att_ids',
//            'status',
            [
                'attribute' => 'status',
                'label'=>'推送状态',
                'attribute'=>'status',
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

//            'created_at',
            [
                'attribute' => 'created_at',
                'label' => '创建时间',
                'value' => function($model){
                    return date('Y-m-d H:i:s',$model->created_at);
                }
            ]
        ],
    ]) ?>

</div>
