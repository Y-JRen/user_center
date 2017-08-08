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

$this->title = '提现审批';

$this->registerJsFile('/dist/plugins/daterangepicker/moment.min.js', [
    'depends' => ['backend\assets\AdminLteAsset']
]);
$this->registerJsFile('/dist/plugins/daterangepicker/daterangepicker.js', [
    'depends' => ['backend\assets\AdminLteAsset']
]);
$this->registerJsFile('/dist/js/user/date.js', [
    'depends' => ['backend\assets\AdminLteAsset']
]);

$statusColumnArray = [
    'attribute' => 'orderStatus',
];
if (ArrayHelper::getValue($this->context, 'history')) {
    $statusColumnArray['class'] = FilterColumn::className();
    $statusColumnArray['filterArray'] = Order::$cashStatusArray;
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
            'attribute' => 'userInfo.real_name',
            'value' => function ($model) {
                return ArrayHelper::getValue($model->userInfo, 'real_name', '--');
            }
        ],
        [
            'attribute' => 'user.phone',
            'format' => 'raw',
            'value' => function ($model) {
                return Html::a(ArrayHelper::getValue($model->user, 'phone'), ['/user/view', 'uid' => $model->uid]);
            }
        ],
        [
            'attribute' => 'order_id',
            'format' => 'raw',
            'value' => function ($model) {
                return Html::a($model->order_id, 'javascript:void(0)', [
                    'data-url' => \yii\helpers\Url::to(['/order/view', 'id' => $model->id]),
                    'class' => 'markOrder'
                ]);
            }
        ],
        [
            'class' => FilterColumn::className(),
            'attribute' => 'platform',
            'value' => function ($model) {
                return ArrayHelper::getValue(Config::$platformArray, $model->platform);
            },
            'filterArray' => Config::$platformArray
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
        'updated_at:datetime',
        [
            'class' => 'yii\grid\ActionColumn',
            'header' => '操作',
            'template' => '{pass} {fail} {reason} {empty}',
            'buttons' => [
                'pass' => function ($url, $model, $key) {
                    return $model->status == Order::STATUS_PROCESSING ? Html::a('审批通过', 'JavaScript:void(0);', ['class' => 'markPass',
                        'data-url' => Url::to(['/cash/pass', 'id' => $model->id])]) : null;
                },
                'fail' => function ($url, $model, $key) {
                    return $model->status == Order::STATUS_PROCESSING ? Html::a('审批不通过', 'JavaScript:void(0);', ['class' => 'markFail',
                        'data-url' => Url::to(['/cash/fail-form', 'id' => $model->id])]) : null;
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
    ],
]); ?>
<?php Pjax::end() ?>
<?php ActiveForm::end(); ?>

<?php $this->beginBlock('javascript') ?>
<script type="text/javascript">
    $(document).ready(function () {
        $(".markPass").click(function () {
            var url = $(this).attr('data-url');
            layer.open({
                title: '审批通过',
                content: '<div class="hint-block">确认通过该次提现申请吗？请在确定前确认客户的资金流水记录。</div>',
                yes: function (index, layero) {
                    $.post(url);
                    layer.close(index); //如果设定了yes回调，需进行手工关闭
                }
            });

        });

        $(".markFail").click(function () {
            var url = $(this).attr('data-url');
            $.get(url, function (html) {
                layer.open({
                    type: 1,
                    title: '审批不通过',
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

        $(".markOrder").click(function () {
            var url = $(this).attr('data-url');
            $.get(url, function (html) {
                layer.open({
                    title: '交易单详情',
                    area: '600px',
                    shadeClose: true,
                    content: html
                });
            })
        });
    })
</script>
<?php $this->endBlock() ?>
