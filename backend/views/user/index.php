<?php

use passport\helpers\Config;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
//use yii\grid\GridView;
use yii\widgets\ActiveForm;
use yii\widgets\Pjax;
use backend\grid\FilterColumn;
use backend\grid\GridView;
use common\models\User;


/* @var $this yii\web\View */
/* @var $searchModel backend\models\search\UserSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '用户管理';
$this->registerJsFile('/dist/plugins/daterangepicker/moment.min.js', [
    'depends' => ['backend\assets\AdminLteAsset']
]);
$this->registerJsFile('/dist/plugins/daterangepicker/daterangepicker.js', [
    'depends' => ['backend\assets\AdminLteAsset']
]);
$this->registerJsFile('/dist/js/user/date.js', [
    'depends' => ['backend\assets\AdminLteAsset']
]);
?>

    <?php Pjax::begin(); ?>

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $this->render('_search2', ['model' => $searchModel]) ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            [
                'attribute' => 'phone',
                'format' => 'raw',
                'value' => function ($model) {
                    return Html::a($model->phone, ['/user/view', 'uid' => $model->id]);
                }
            ],
            'user_name',
            'email:email',
            [
                'class' => FilterColumn::className(),
                'attribute' => 'status',
                'value' => function ($model) {
                    return ArrayHelper::getValue(User::$statusArray,$model->status);
                },
                'filterArray' => User::$statusArray,
            ],
            [
                'class' => FilterColumn::className(),
                'attribute' => 'from_platform',
                'value' => function ($model) {
                    return ArrayHelper::getValue(Config::getPlatformArray(), $model->from_platform);
                },
                'filterArray' => Config::getPlatformArray()
            ],
            'from_channel',
            [
                'attribute' => 'reg_time',
                'label' => '注册时间',
                'format' => 'datetime',
                'enableSorting' => true
            ],
            'reg_ip',
            [
                'attribute' => 'login_time',
                'label' => '最后登录时间',
                'format' => 'datetime',
                'enableSorting' => true
            ],

            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '{view} {order_view} {amount_view}',
                'buttons' => [
                    'view' => function ($url, $model, $key) {
                        return Html::a('详情', ['user/view', 'id' => $model->id], ['class' => 'btn btn-success btn-xs']);
                    },
                    'order_view' => function ($url, $model, $key) {
                        return Html::a('订单详情', ['user/order', 'uid' => $model->id], ['class' => 'btn btn-success btn-xs']);
                    },
                    'amount_view' => function ($url, $model, $key) {
                        return Html::a('资金详情', ['user/amount', 'uid' => $model->id], ['class' => 'btn btn-success btn-xs']);
                    }
                ]
            ],
        ],
    ]); ?>
    <?php ActiveForm::end(); ?>
    <?php Pjax::end(); ?>
