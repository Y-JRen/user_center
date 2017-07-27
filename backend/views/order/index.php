<?php

use backend\grid\FilterColumn;
use backend\grid\GridView;
use passport\helpers\Config;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\widgets\Pjax;
use backend\models\Order;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\search\OrderSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '消费记录';

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

<?php Pjax::begin() ?>

<?php $form = ActiveForm::begin([
    'action' => ['index'],
    'method' => 'get',
]); ?>

<?= $this->render('_search', ['model' => $searchModel]) ?>

<?= GridView::widget([
    'dataProvider' => $dataProvider,
    'columns' => [
        [
            'class' => 'yii\grid\SerialColumn',
            'header' => '序号'
        ],
        [
            'attribute' => 'user.phone',
            'format' => 'raw',
            'value' => function ($model) {
                return Html::a(ArrayHelper::getValue($model->user, 'phone'), ['/user/view', 'uid' => $model->uid]);
            }
        ],
        [
            'class' => FilterColumn::className(),
            'attribute' => 'platform',
            'value' => function ($model) {
                return ArrayHelper::getValue(Config::getPlatformArray(), $model->platform);
            },
            'filterArray' => Config::getPlatformArray()
        ],
        'platform_order_id',
        'order_id',
        [
            'class' => FilterColumn::className(),
            'attribute' => 'order_type',
            'value' => function ($model) {
                return $model->type;
            },
            'filterArray' => Order::getTypeName()
        ],
        [
            'class' => FilterColumn::className(),
            'attribute' => 'order_subtype',
            'value' => function ($model) {
                return ArrayHelper::getValue(Order::$subTypeName, $model->order_subtype, $model->order_subtype);
            },
            'filterArray' => Order::$subTypeName
        ],
        'receipt_amount:currency',
        [
            'attribute' => 'created_at',
            'format' => 'datetime',
            'enableSorting' => true
        ],
        [
            'attribute' => 'updated_at',
            'format' => 'datetime',
            'enableSorting' => true
        ],
        [
            'class' => FilterColumn::className(),
            'attribute' => 'status',
            'value' => function ($model) {
                return $model->orderStatus;
            },
            'filterArray' => Order::getStatus()
        ],
    ],
]); ?>
<?php ActiveForm::end(); ?>
<?php Pjax::end() ?>
