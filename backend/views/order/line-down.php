<?php

use backend\grid\GridView;
use backend\models\Order;
use common\helpers\JsonHelper;
use passport\helpers\Config;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use yii\widgets\Pjax;

use backend\grid\FilterColumn;


/* @var $this yii\web\View */
/* @var $searchModel backend\models\search\OrderSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '收款确认';

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
    $statusColumnArray['filterArray'] = Order::$rechargeStatusArray;
}
?>

<?php $form = ActiveForm::begin(['action' => ['line-down'], 'method' => 'get']); ?>

<?= $this->render('_search_recharge', ['model' => $searchModel]) ?>

<div class="mb-md clearfix">
    <?= Html::a('导出列表', Yii::$app->request->getUrl(), [
        'class' => 'btn btn-primary btn-sm mr-md pull-left',
        'data-method' => 'post']) ?>
</div>

<?php Pjax::begin() ?>

<?= GridView::widget(['dataProvider' => $dataProvider,
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
            'label' => '银行名称',
            'value' => function ($model) {
                return ArrayHelper::getValue(ArrayHelper::getValue(JsonHelper::BankHelper($model->remark), 'bankName'), 'value');
            }
        ],
        [
            'label' => '姓名',
            'value' => function ($model) {
                return ArrayHelper::getValue(ArrayHelper::getValue(JsonHelper::BankHelper($model->remark), 'accountName'), 'value');
            }
        ],
        [
            'label' => '流水单号',
            'value' => function ($model) {
                return ArrayHelper::getValue(ArrayHelper::getValue(JsonHelper::BankHelper($model->remark), 'referenceNumber'), 'value');
            }
        ],
        [
            'class' => FilterColumn::className(),
            'attribute' => 'order_subtype',
            'value' => function ($model) {
                return ArrayHelper::getValue(Order::$subTypeName, $model->order_subtype, $model->order_subtype);
            },
            'filterArray' => Order::$subTypeName
        ],
        [
            'label' => '充值金额',
            'attribute' => 'receipt_amount',
            'format' => 'currency'
        ],
        'created_at:datetime',
        $statusColumnArray,
        [
            'class' => 'yii\grid\ActionColumn',
            'header' => '操作',
            'template' => '{pass} {fail} {reason} {empty}',
            'buttons' => [
                'pass' => function ($url, $model, $key) {
                    return $model->status == Order::STATUS_PENDING ? Html::a('确认收款', 'JavaScript:void(0);', ['class' => 'markPass',
                        'data-url' => Url::to(['/order/line-down-pass', 'id' => $model->id])]) : null;
                },
                'fail' => function ($url, $model, $key) {
                    return $model->status == Order::STATUS_PENDING ? Html::a('未收到款', 'JavaScript:void(0);', ['class' => 'markFail',
                        'data-url' => Url::to(['/order/line-down-fail', 'id' => $model->id])]) : null;
                },
                'reason' => function ($url, $model, $key) {
                    return $model->status == Order::STATUS_FAILED ? Html::a('查看原因', 'JavaScript:void(0);', ['class' => 'markReason',
                        'data-url' => Url::to(['/log-review/fail', 'orderId' => $model->id])]) : null;
                },
                'empty' => function ($url, $model, $key) {
                    return $model->status == Order::STATUS_SUCCESSFUL ? '--' : null;
                }
            ]
        ]
    ]]);
?>
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
                    title: '收款确认',
                    area: '600px',
                    shadeClose: true,
                    content: html
                });
            })
        });

        $(".markFail").click(function () {
            var url = $(this).attr('data-url');
            $.get(url, function (html) {
                layer.open({
                    type: 1,
                    title: '未到账确认',
                    area: '600px',
                    shadeClose: true,
                    content: html
                });
            })
        });

        $(".markReason").click(function () {
            var url = $(this).attr('data-url');
            $.get(url, function (html) {
                layer.open({
                    title: '原因',
                    shadeClose: true,
                    content: html
                });
            })
        });
    })
</script>
<?php $this->endBlock() ?>
