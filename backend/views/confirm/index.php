<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */


?>
<div class="recharge-confirm-index">

    <h1><?= Html::encode($this->title) ?></h1>


    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

//            'id',
            'order_id',
//            'account_id',
            'account',
            'back_order',
             'org',
            // 'org_id',
            // 'type_id',
             'type',
             'transaction_time:datetime',
            // 'remark:ntext',
             'amount',
            // 'att_ids',
//             'status',
            [
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
            // 'method',
            [
                'attribute' => 'method',
                'label' => '支付方式',
                'value' => function($model){
                    if($model->method==1){
                        return '支付宝';
                    }elseif($model->method==2){
                        return '微信';
                    }elseif($model->method==3){
                        return '银行卡';
                    }
                }
            ],
            // 'created_at',

            ['class' => 'yii\grid\ActionColumn',
                'template' => '{view}',
                'buttons' => [
                    'view' => function ($url) {
                        return Html::a('<span>查看</span>', $url);
                    },
                ],
            ],
        ],
    ]); ?>
</div>
