<?php

use backend\models\Order;
use common\helpers\JsonHelper;
use passport\helpers\Config;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use yii\widgets\Pjax;

use backend\grid\FilterColumn;


/* @var $this yii\web\View */
/* @var $searchModel backend\models\search\OrderSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '打款确认';

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
    'action' => ['line-down'],
    'method' => 'get',
]); ?>
<?= $this->render('_search2', ['model' => $searchModel]) ?>

<?= GridView::widget([
    'dataProvider' => $dataProvider,
    'columns' => [
        ['class' => 'yii\grid\SerialColumn'],

        [
            'attribute' => 'uid',
            'label' => '用户',
            'format' => 'raw',
            'value' => function ($model) {
                $phone = \common\models\User::findOne($model->uid)->phone;
                return Html::a($phone, ['/user/view', 'uid' => $model->uid]);
            }
        ],
        'order_id',
        [
            'attribute' => 'order_type',
            'value' => function ($model) {
                return $model->type;
            },
            'filter' => Order::getTypeName()
        ],
        [
            'class' => FilterColumn::className(),
            'attribute' => 'platform',
            'value' => function ($model) {
                return ArrayHelper::getValue(Config::getPlatformArray(), $model->platform);
            },
            'filterArray' => Config::getPlatformArray(),
        ],
        [
            'class' => FilterColumn::className(),
            'attribute' => 'order_subtype',
            'value' => function ($model) {
                return ArrayHelper::getValue(Order::$subTypeName, $model->order_subtype, $model->order_subtype);
            },
            'filterArray' => Order::$subTypeName,
        ],
        [
            'label' => '充值金额',
            'attribute' => 'receipt_amount',
            'format' => 'currency'
        ],
        [
            'label' => '银行名称',
            'value' => function ($model) {
                return ArrayHelper::getValue(ArrayHelper::getValue(JsonHelper::BankHelper($model->remark), 'bankName'), 'value');
            },
        ],
        [
            'label' => '姓名',
            'value' => function ($model) {
                return ArrayHelper::getValue(ArrayHelper::getValue(JsonHelper::BankHelper($model->remark), 'accountName'), 'value');
            },
        ],
        [
            'label' => '流水单号',
            'value' => function ($model) {
                return ArrayHelper::getValue(ArrayHelper::getValue(JsonHelper::BankHelper($model->remark), 'referenceNumber'), 'value');
            },
        ],
        [
            'class' => FilterColumn::className(),
            'attribute' => 'status',
            'value' => function ($model) {
                return Order::getStatus($model->status);
            },
            'filterArray' => Order::getStatusName()
        ],
        'created_at:datetime',
        [
            'label' => '操作',
            'format' => 'raw',
            'value' => function ($data) {
                return Html::button('确认充值', [
                    'data-toggle' => "modal",
                    'data-target' => "#modal",
                    'class' => 'btn btn-success modalClass btn-xs',
                    'url' => Url::to(['/order/line-down-form', 'id' => $data->id])
                ])
                . '&nbsp;&nbsp;' .
                Html::a('充值失败',
                    ['/order/confirm-fail', 'id' => $data->id],
                    ['class' => 'btn btn-primary btn-xs', 'data-confirm' => '确定要设置为充值失败吗？', 'data-method' => 'post']
                );
            }
        ]
    ],
]); ?>
<?php ActiveForm::end(); ?>
<?php Pjax::end() ?>


    <div class="modal fade" id="modal-default">
        <div class="modal-dialog">
            <div class="modal-content">
            </div>
            <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
    </div>
    <script>
        var getAccountUrl = '<?= Url::to(['finance/get-accounts']) ?>';
        var getTypeUrl = '<?= Url::to(['finance/get-tag']) ?>';
    </script>
<?php

$js = <<<_SCRIPT
    $('.modalClass').click(function () {
        $.get($(this).attr('url'),function (html) {
            $('.modal-content').html(html);
            $('#modal-default').modal('show')
        });
    });
_SCRIPT;
$this->registerJs($js);
