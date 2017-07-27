<?php

use backend\grid\GridView;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model common\models\User */

$this->title = '会员详情';
$this->params['breadcrumbs'][] = ['label' => '用户管理', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
    <div class="row mb-md">
        <div class="col-sm-12 col-xs-12 nav-tabs-custom">
            <ul class="nav nav-tabs">
                <li><a href="<?= Url::to(['user/view', 'uid' => $uid]) ?>">客户信息</a></li>
                <li class="active"><a href="<?= Url::to(['user/fund-record', 'uid' => $uid]) ?>">资金明细</a></li>
                <li><a href="<?= Url::to(['', 'uid' => '']) ?>">订单记录</a></li>
            </ul>
        </div>
    </div>
<?= GridView::widget([
    'dataProvider' => $dataProvider,
    'columns' => [
        [
            'class' => 'yii\grid\SerialColumn',
            'header' => '序号'
        ],
        [
            'attribute' => 'created_at',
            'format' => 'datetime',
            'enableSorting' => true
        ],
        [
            'attribute' => 'order_id',
        ],

        [
            'attribute' => 'order_type',
            'value' => function ($model) {
                return ArrayHelper::getValue($model->typename, $model->order_type);
            }
        ],
        [
            'attribute' => 'order_subtype',
        ],
        'amount:currency',
        [
            'attribute' => 'status',
            'value' => function ($model) {
                return ArrayHelper::getValue($model->statusname, $model->status);
            }
        ],
        [
            'attribute' => 'desc',
        ],

    ],
]); ?>