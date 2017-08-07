<?php

use passport\helpers\Config;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\widgets\Pjax;
use backend\grid\FilterColumn;
use backend\grid\GridView;
use common\models\User;


/* @var $this yii\web\View */
/* @var $searchModel backend\models\search\UserSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $totalBalance integer */
/* @var $totalBalance $totalFreeze */

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

<?= $this->render('_search', ['model' => $searchModel]) ?>
<div class="mb-md clearfix">
    <?= Html::a('导出列表', Yii::$app->request->getUrl(), [
        'class' => 'btn btn-primary btn-sm mr-md pull-left',
        'data-method' => 'post']) ?>
</div>
<?= GridView::widget([
    'dataProvider' => $dataProvider,
    'columns' => [
        [
            'class' => 'yii\grid\SerialColumn',
            'header' => '序号',
        ],

        [
            'attribute' => 'phone',
            'format' => 'raw',
            'value' => function ($model) {
                return Html::a($model->phone, ['/user/view', 'uid' => $model->id]);
            }
        ],
        [
            'attribute' => 'userInfo.real_name',
            'value' => function ($model) {
                return ArrayHelper::getValue($model->userInfo, 'real_name', '--');
            }
        ],
        [
            'class' => FilterColumn::className(),
            'attribute' => 'from_platform',
            'value' => function ($model) {
                return ArrayHelper::getValue(Config::$platformArray, $model->from_platform);
            },
            'filterArray' => Config::$platformArray
        ],
        [
            'attribute' => 'reg_time',
            'format' => 'datetime',
            'enableSorting' => true
        ],
        [
            'attribute' => 'login_time',
            'format' => 'datetime',
            'enableSorting' => true
        ],
        'reg_ip',
        [
            'label' => '可用余额',
            'format' => 'currency',
            'value' => function ($model) {
                return ArrayHelper::getValue($model->balance, 'amount');
            }
        ],
        [
            'label' => '冻结金额',
            'format' => 'currency',
            'value' => function ($model) {
                return ArrayHelper::getValue($model->freeze, 'amount');
            }
        ],
        [
            'class' => FilterColumn::className(),
            'attribute' => 'status',
            'value' => function ($model) {
                return ArrayHelper::getValue(User::$statusArray, $model->status);
            },
            'filterArray' => User::$statusArray,
        ],
        [
            'class' => 'yii\grid\ActionColumn',
            'template' => '{view} {order_view} {amount_view}',
            'header' => '操作',
            'buttons' => [
                'view' => function ($url, $model, $key) {
                    return Html::a('详情', ['user/view', 'uid' => $model->id]);
                }
            ]
        ],
    ],
]); ?>
<div class="row">
    <div class="col-xs-12">
        <p>总计：</p>
        <p><b>可用余额:</b><?= Yii::$app->formatter->asCurrency($totalBalance) ?></p>
        <p><b>冻结余额:</b><?= Yii::$app->formatter->asCurrency($totalFreeze) ?></p>
    </div>
</div>
<?php ActiveForm::end(); ?>
<?php Pjax::end(); ?>
