<?php

use backend\models\Coupon;
use backend\models\Platform;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\Coupon */
/* @var $form yii\widgets\ActiveForm */

$this->registerCssFile('/datetimepicker/css/bootstrap-datetimepicker.min.css', ['depends' => 'yii\bootstrap\BootstrapAsset']);
$this->registerJsFile('/datetimepicker/js/bootstrap-datetimepicker.min.js', ['depends' => 'yii\web\JqueryAsset']);
?>


<?php $form = ActiveForm::begin([
    'options' => ['class' => 'form-horizontal'],
    'fieldConfig' => [
        'template' => "{label}<div class='col-sm-8'>{input}</div>{hint}\n{error}",
        'labelOptions' => ['class' => 'control-label col-sm-2']
    ]]);
?>

    <div class="box-body">

        <?= $form->field($model, 'platform_id')->dropDownList(ArrayHelper::map(Platform::find()->all(), 'id', 'name_cn')) ?>

        <?= $form->field($model, 'dealer_id')->dropDownList([]) ?>

        <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'short_name')->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'image')->fileInput() ?>

        <?= $form->field($model, 'type')->dropDownList(Coupon::$typeArray) ?>

        <?= $form->field($model, 'number')->textInput() ?>

        <?= $form->field($model, 'amount')->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'effective_way')->radioList(Coupon::$effectiveWayArray) ?>

        <div class="markEffectiveWay mark<?= Coupon::EFFECTIVE_WAY_FIXED ?>" style="display: none;">
            <?= $form->field($model, 'start_time')->textInput(['class' => 'form-control markDataTime']) ?>

            <?= $form->field($model, 'end_time')->textInput(['class' => 'form-control markDataTime']) ?>
        </div>

        <div class="markEffectiveWay mark<?= Coupon::EFFECTIVE_WAY_IMMEDIATE ?>" style="display: none;">
            <?= $form->field($model, 'start_time')->dropDownList(Coupon::$startTimeArray)->label('领取后') ?>

            <?= $form->field($model, 'end_time')->textInput()->label('生效时长')->hint('天') ?>
        </div>

        <?= $form->field($model, 'upper_limit')->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'superposition')->radioList(Coupon::$superpositionArray) ?>

        <?= $form->field($model, 'tips')->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'desc')->textarea() ?>

        <?= $form->field($model, 'status')->dropDownList(Coupon::$statusArray) ?>

        <?= $form->field($model, 'receive_start_time')->textInput(['class' => 'form-control markDataTime']) ?>

        <?= $form->field($model, 'receive_end_time')->textInput(['class' => 'form-control markDataTime']) ?>
    </div>


    <div class="box-footer">
        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

<?php ActiveForm::end(); ?>

<?php
$js = <<<JS
    $('.markDataTime').datetimepicker({
        format: 'yyyy-mm-dd hh:00:00',
        pickerPosition: 'top-right',
        autoclose: true,//自动关闭
        minView: 1//最精准的时间选择为日期0-分 1-时 2-日 3-月
    });

    $('input[name="Coupon[effective_way]"]').change(function() {
       effectiveShow();
    });
    
    function effectiveShow() {
        var effective_way = $('input[name="Coupon[effective_way]"]:checked').val();
        $('.markEffectiveWay').hide();
        $(".mark"+effective_way).show();
    }
    
    getDealer();
    
    $('#coupon-platform_id').change(function(){
        getDealer();
    });
    
    function getDealer() {
      $.get('/dealer/ajax', {platform_id:$("#coupon-platform_id").val()},function(data){
            var html = '';
            for(var i in data) {
                html += '<option value="'+i+'">'+data[i]+'</option>';
            };
            $('#coupon-dealer_id').html(html);
        },'json')
    }
JS;

$this->registerJs($js, $this::POS_END);

