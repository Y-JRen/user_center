<?php

use backend\grid\FilterColumn;
use backend\grid\GridView;
use backend\models\Order;
use common\helpers\JsonHelper;
use passport\helpers\Config;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\search\OrderSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '银行待转账记录';
$this->registerJsFile('/dist/plugins/daterangepicker/moment.min.js', [
    'depends' => ['backend\assets\AdminLteAsset']
]);
$this->registerJsFile('/dist/plugins/daterangepicker/daterangepicker.js', [
    'depends' => ['backend\assets\AdminLteAsset']
]);
$this->registerJsFile('/dist/js/user/date.js', [
    'depends' => ['backend\assets\AdminLteAsset']
]);
$this->registerJsFile('/dist/js/web.js', [
    'depends' => ['yii\web\JqueryAsset']
]);
$this->registerCssFile('/dist/plugins/datetimepicker/css/bootstrap-datetimepicker.min.css', [
    'depends' => ['yii\bootstrap\BootstrapAsset']
]);
$this->registerJsFile('/dist/plugins/datetimepicker/js/bootstrap-datetimepicker.min.js', [
    'depends' => ['yii\bootstrap\BootstrapAsset']
]);


$statusColumnArray = [
    'attribute' => 'orderStatus',
];
if (ArrayHelper::getValue($this->context, 'history')) {
    $statusColumnArray['class'] = FilterColumn::className();
    $statusColumnArray['filterArray'] = Order::getStatusName();
}


?>
<?php $form = ActiveForm::begin(['method' => 'get']); ?>
<?= $this->render('/order/_search_recharge', ['model' => $searchModel]) ?>

<div class="mb-md clearfix">
    <?= Html::a('导出列表', Yii::$app->request->getUrl(), [
        'class' => 'btn btn-primary btn-sm mr-md pull-left',
        'data-method' => 'post']) ?>
</div>

<?php Pjax::begin() ?>
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
        'order_id',
        [
            'class' => FilterColumn::className(),
            'attribute' => 'platform',
            'value' => function ($model) {
                return ArrayHelper::getValue(Config::getPlatformArray(), $model->platform);
            },
            'filterArray' => Config::getPlatformArray()
        ],
        [
            'label' => '到账银行名称',
            'value' => function ($model) {
                return ArrayHelper::getValue(ArrayHelper::getValue(JsonHelper::BankHelper($model->remark), 'bankName'), 'value');
            }
        ],
        [
            'label' => '到账银行卡',
            'value' => function ($model) {
                return ArrayHelper::getValue(ArrayHelper::getValue(JsonHelper::BankHelper($model->remark), 'bankCard'), 'value');
            }
        ],
        'receipt_amount:currency',
        $statusColumnArray,
        'created_at:datetime:申请时间',
        [
            'label' => '审批人',
            'value' => function ($model) {
                return $model->cashUser;
            }
        ],
        ['class' => 'yii\grid\ActionColumn',
            'header' => '操作',
            'template' => '{pass} {empty}',
            'buttons' => [
                'pass' => function ($url, $model, $key) {
                    return $model->status == Order::STATUS_SUCCESSFUL ? Html::a('确认已打款', 'JavaScript:void(0);', ['class' => 'markPass',
                        'data-url' => Url::to(['/transfer/confirm-form', 'id' => $model->id])]) : null;
                },
                'empty' => function ($url, $model, $key) {
                    return $model->status == Order::STATUS_TRANSFER ? '--' : null;
                }
            ]
        ]
    ],
]); ?>
<?php Pjax::end() ?>
<?php ActiveForm::end(); ?>

<?php $this->beginBlock('javascript') ?>
<script type="text/javascript">
    var getAccountUrl = '<?= Url::to(['finance/get-accounts']) ?>';
    var getTypeUrl = '<?= Url::to(['finance/get-tag']) ?>';

    $(document).ready(function () {
        $(".markPass").click(function () {
            var url = $(this).attr('data-url');
            $.get(url, function (html) {
                layer.open({
                    type: 1,
                    title: '确认已打款',
                    area: '600px',
                    shadeClose: true,
                    content: html
                });
            })
        });
    })
</script>
<?php $this->endBlock() ?>
