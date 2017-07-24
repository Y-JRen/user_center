<?php

use passport\helpers\Config;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\ActiveForm;
use yii\widgets\Pjax;
use backend\models\Order;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\search\OrderSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '消费记录';
$this->params['breadcrumbs'][] = $this->title;

$this->registerJsFile('/dist/plugins/daterangepicker/moment.min.js', [
    'depends' => ['backend\assets\AdminLteAsset']
]);
$this->registerJsFile('/dist/plugins/daterangepicker/daterangepicker.js', [
    'depends' => ['backend\assets\AdminLteAsset']
]);
$this->registerJsFile('/dist/js/user/date.js', [
    'depends' => ['backend\assets\AdminLteAsset']
]);

$this->registerJsFile('/dist/plugins/dataTables/jquery.dataTables.min.js', [
    'depends' => ['backend\assets\AdminLteAsset']
]);
$this->registerJsFile('/dist/plugins/dataTables/jquery.dataTables.bootstrap.js', [
    'depends' => ['backend\assets\AdminLteAsset']
]);
$this->registerJsFile('/dist/js/meTables.js', [
    'depends' => ['backend\assets\AdminLteAsset']
]);

$this->registerJsFile('/dist/plugins/vue-element/vue.min.js', [
    'depends' => ['backend\assets\AdminLteAsset']
]);

$this->registerJsFile('/dist/plugins/vue-element/index.js', [
    'depends' => ['backend\assets\AdminLteAsset']
]);

$this->registerCssFile('/dist/plugins/vue-element/index.css', [
    'depends' => ['backend\assets\AdminLteAsset']
]);

$this->registerCssFile('/dist/plugins/dataTables/css/jquery.dataTables.min.css', [
    'depends' => ['backend\assets\AdminLteAsset']
]);
?>


<?php Pjax::begin() ?>
<?= $this->render('_search', ['model' => $searchModel]) ?>

<?= GridView::widget([
    'dataProvider' => $dataProvider,
    'columns' => [
        ['class' => 'yii\grid\SerialColumn'],

        [
            'attribute' => 'uid',
            'label' => '用户手机号',
            'value' => function ($model) {
                return \common\models\User::findOne($model->uid)->phone;
            }
        ],
        [
            'attribute' => 'platform_order_id',
            'label' => "电商平台订单号 <i title=\"Filter Menu\" class=\"fa fa-filter lte-dropdown-trigger ml-0\"></i>",
            'format' => 'html'
        ],
        [
            'attribute' => 'order_id',
            'label' => '用户中心订单号',
        ],
        [
            'attribute' => 'order_type',
            'value' => function ($model) {
                return $model->type;
            },
        ],
        [
            'attribute' => 'order_subtype',
            'value' => function ($model) {
                return ArrayHelper::getValue(Order::$subTypeName, $model->order_subtype, $model->order_subtype);
            },
        ],
        'receipt_amount:currency',
        [
            'attribute' => 'status',
            'value' => function ($model) {
                return Order::getStatus($model->status);
            },
        ],
        [
            'attribute' => 'platform',
            'value' => function ($model) {
                return ArrayHelper::getValue(Config::getPlatformArray(), $model->platform);
            },
        ],
        'created_at:datetime',
        'updated_at:datetime',
        ['class' => 'yii\grid\ActionColumn', 'template' => '{view}'],
    ],
]); ?>
<?php Pjax::end() ?>
