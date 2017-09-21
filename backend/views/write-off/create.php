<?php

/* @var $model \backend\models\Order */

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

?>

<?php $form = ActiveForm::begin([
    'options' => ['class' => 'form-horizontal'],
    'fieldConfig' => [
        'template' => "{label} <div class='col-sm-8'> {input} </div>{hint}",
        'labelOptions' => ['class' => 'control-label col-sm-2']
    ]]);
?>

<div class="box-body">
    <?= $form->field($model, 'phone')->label('会员手机号码') ?>

    <div class="form-group">
        <label class="control-label col-sm-2">客户信息</label>
        <div class="col-sm-8 mark_user">
            <p>请填写用户手机号,查看用户相关信息</p>
        </div>
    </div>

    <?= $form->field($model, 'order_type')->radioList([3 => '增加', '2' => '扣减'])->label('操作方式') ?>

    <?= $form->field($model, 'desc')->label('简述')->textInput(['placeholder' => '例：扣除保险金额; 活动返现;']) ?>

    <?= $form->field($model, 'amount')->label('核销金额') ?>

    <?= $form->field($model, 'platform_order_id')->label('电商订单号')->textInput(['placeholder' => '针对电商那笔订单进行核销，可不填']) ?>

    <?= $form->field($model, 'remark')->textarea()->label('原因') ?>
</div>


<div class="box-footer text-right" style="padding-right: 40px;">
    <?= Html::button('取消', ['class' => 'btn btn-primary mark_cancel']) ?>
    <?= Html::button('确定', ['class' => 'btn btn-primary mark_submit']) ?>
</div>

<?php ActiveForm::end(); ?>
