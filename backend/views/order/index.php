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

$this->title = '交易记录明细';

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


<?php $form = ActiveForm::begin([
    'action' => ['index'],
    'method' => 'get',
]);
?>
<?= $this->render('_search', ['model' => $searchModel]) ?>
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
        [
            'class' => FilterColumn::className(),
            'attribute' => 'platform',
            'value' => function ($model) {
                return ArrayHelper::getValue(Config::$platformArray, $model->platform);
            },
            'filterArray' => Config::$platformArray
        ],
        [
            'attribute' => 'platform_order_id',
            'value' => function ($model) {
                $platform_order_id = $model->platform_order_id;
                if (empty($platform_order_id)) {
                    return '--';
                } else {
                    return $platform_order_id;
                }
            },
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
            'attribute' => 'orderStatus',
            'value' => function ($model) {
                return ArrayHelper::getValue(Order::getStatusName(), $model->status);
            },
            'filterArray' => Order::getStatusName(),
        ],
    ],
]); ?>
<?php Pjax::end() ?>
<?php ActiveForm::end(); ?>

<?php $this->beginBlock('javascript') ?>
    <script type="text/javascript">
        $(document).ready(function () {
            $('body').delegate(".markOrder", "click", function () {
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