<?php

use backend\grid\FilterColumn;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\widgets\Pjax;
use backend\grid\GridView;
use yii\helpers\Url;

$this->title = '资金核销';

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

<?= $this->render('_search') ?>

    <div class="mb-md clearfix">
        <?= Html::a('核销', 'javascript:void(0);', [
            'class' => 'btn btn-primary btn-sm mr-md pull-left mark_new']) ?>
    </div>

<?php Pjax::begin() ?>
<?= GridView::widget([
    'dataProvider' => $dataProvider,
    'columns' => [
        [
            'class' => 'yii\grid\SerialColumn',
            'header' => '序号'
        ],
        'created_at:datetime:操作时间',
        [
            'attribute' => 'user.phone',
            'format' => 'raw',
            'value' => function ($model) {
                return Html::a(ArrayHelper::getValue($model->user, 'phone'), ['/user/view', 'uid' => $model->uid], ['target' => '_blank']);
            }
        ],
        'order_id:text:核销单号',
        [
            'class' => FilterColumn::className(),
            'attribute' => 'order_type',
            'value' => function ($model) {
                return $model->order_type == 2 ? '扣减' : '增加';
            },
            'label' => '核销方式',
            'filterArray' => [2 => '扣减', 3 => '增加']
        ],
        [
            'label' => '核销前金额',
            'format' => 'currency',
            'value' => function ($model) {
                return ArrayHelper::getValue($model->poolBalance, 'before_amount', 0);
            }
        ],
        [
            'label' => '核销金额',
            'format' => 'currency',
            'value' => function ($model) {
                return (($model->order_type == 2) ? '-' : '+') . $model->receipt_amount;
            }
        ],
        [
            'label' => '核销后金额',
            'format' => 'currency',
            'value' => function ($model) {
                return ArrayHelper::getValue($model->poolBalance, 'after_amount', 0);
            }
        ],
        'desc:text:简述',
        'remark:text:原因',
    ],
]); ?>
<?php Pjax::end() ?>
<?php ActiveForm::end(); ?>

<?php $this->beginBlock('javascript') ?>
    <script type="text/javascript">
        $(document).ready(function () {
            // 核销按钮
            $(".mark_new").click(function () {
                $.get("<?= Url::to(['/write-off/create']) ?>", function (html) {
                    layer.open({
                        type: 1,
                        title: '核销',
                        area: '600px',
                        shadeClose: true,
                        content: html
                    });
                });
            });

            // 手机号搜索
            $('body').delegate("#order-phone", "blur", function () {
                var phone = $("#order-phone").val();
                $.get("<?=Url::to(['user/phone'])?>", {phone: phone}, function (html) {
                    $(".mark_user").html(html);
                })
            });

            $('body').delegate(".mark_submit", "click", function () {
                $(this).prop("disabled", true);
                var url = $('#createForm').attr('action');
                $.post(url, $('#createForm').serialize(), function (result) {
                    if (result.status) {
                        layer.closeAll();
                        location.reload();
                    } else {
                        $('.mark_submit').prop("disabled", false);
                        layer.msg(result.msg, {icon: 2});
                    }
                });
            });

            $('body').delegate(".mark_cancel", "click", function () {
                layer.closeAll();
            });
        });
    </script>
<?php $this->endBlock() ?>