<?php

use backend\grid\FilterColumn;
use backend\grid\GridView;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use backend\models\Order;
use yii\widgets\ActiveForm;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $model common\models\User */

$this->title = '会员详情';
$this->params['breadcrumbs'][] = ['label' => '用户管理', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<?= $this->render('_top_header'); ?>


<?php Pjax::begin(); ?>

<?php $form = ActiveForm::begin([
    'action' => ["fund-record?uid=$uid"],
    'method' => 'get',
]); ?>


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
            'class' => FilterColumn::className(),
            'attribute' => 'order_type',
            'value' => function ($model) {
                return $model->type;
            },
            'filterArray' => Order::getTypeName(),
        ],
        [
            'class' => FilterColumn::className(),
            'attribute' => 'order_subtype',
            'value' => function ($model) {
                return ArrayHelper::getValue(Order::$subTypeName, $model->order_subtype, $model->order_subtype);
            },
            'filterArray' => Order::$subTypeName
        ],
        'amount:currency',
        [
            'class' => FilterColumn::className(),
            'attribute' => 'orderStatus',
            'value' => function ($model) {
                return ArrayHelper::getValue(Order::getStatusName(), $model->status);
            },
            'filterArray' => Order::getStatusName(),
        ],
        'desc',

    ],
]); ?>

<?php ActiveForm::end(); ?>
<?php Pjax::end(); ?>
