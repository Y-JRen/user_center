<?php

use backend\models\Order;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\models\search\OrderSearch */
/* @var $form yii\widgets\ActiveForm */
?>
    <blockquote>
        <div class="order-search row">

            <?php $form = ActiveForm::begin([
                'action' => ['index'],
                'method' => 'get',
                'options' => ['class' => 'form-inline']
            ]); ?>

            <?= $form->field($model, 'uid', ['options' => ['class' => 'col-sm-4']])->label('用户手机') ?>

            <?= $form->field($model, 'platform_order_id', ['options' => ['class' => 'col-sm-4']])->label('电商订单') ?>

            <?= $form->field($model, 'order_id', ['options' => ['class' => 'col-sm-4']])->label('用户中心订单号') ?>

            <?= $form->field($model, 'order_type', ['options' => ['class' => 'col-sm-2']])->dropDownList(Order::getTypeName(), ['prompt' => '']) ?>

            <?= $form->field($model, 'order_subtype', ['options' => ['class' => 'col-sm-2']])->dropDownList(['alipay' => '支付宝', 'wechat' => '微信', 'lakala' => 'POS机', 'line_down' => '线下充值'], ['prompt' => '']) ?>

            <?= $form->field($model, 'status', ['options' => ['class' => 'col-sm-2']])->dropDownList(Order::getStatusName(), ['prompt' => ''])->label('订单状态') ?>


            <?= $form->field($model, 'created_at', ['options' => ['id' => 'mark_create_time', 'class' => 'col-sm-3']]) ?>

            <div class="form-group">
                <?= Html::submitButton('Search', ['class' => 'btn btn-success']) ?>
                <?= Html::resetButton('Reset', ['class' => 'btn btn-default']) ?>
            </div>

            <?php ActiveForm::end(); ?>

        </div>
    </blockquote>

<?php
$js = <<<JS
        $(function () {
            $('#mark_create_time').daterangepicker({
                autoUpdateInput:false,
                opens: "left",
                locale: {
                    format: 'YYYY-MM-DD',
                    cancelLabel: 'Clear'
                }
            });
              $('#mark_create_time').on('apply.daterangepicker', function(ev, picker) {
                  $("#ordersearch-created_at").val(picker.startDate.format('YYYY-MM-DD') + ' - ' + picker.endDate.format('YYYY-MM-DD'));
              });
            
              $('#mark_create_time').on('cancel.daterangepicker', function(ev, picker) {
                  $("#ordersearch-created_at").val('');
              });
        });
JS;

$this->registerJs($js, $this::POS_END);