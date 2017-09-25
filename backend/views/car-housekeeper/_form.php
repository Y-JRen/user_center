<?php

use common\logic\CheLogic;
use kartik\file\FileInput;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;


/* @var $this yii\web\View */
/* @var $model backend\models\CarHousekeeper */
/* @var $carManagementModel backend\models\CarManagement */
/* @var $form yii\widgets\ActiveForm */


if ($model->isNewRecord) {
    $carManagementModel = new \backend\models\CarManagement();
    $factoryItems = ['请先选择品牌'];
    $seriesItems = ['请先选择厂商'];
    $modelItems = ['请先选择车系'];
} else {
    $carManagementModel = $model->carManagement;
    $factoryItems = CheLogic::instance()->factory($carManagementModel->brand_id);
    $seriesItems = CheLogic::instance()->series($carManagementModel->brand_id, $carManagementModel->factory_id);
    $modelItems = CheLogic::instance()->model($carManagementModel->series_id);
}


$initialPreview = explode(',', $carManagementModel->driving_license);
$initialPreviewConfig = [];
foreach ($initialPreview as $key => $data) {
    $key++;
    $initialPreviewConfig[] = ['caption' => "缩略图({$key})", 'key' => $data];
}

$fileArray = [
    'options' => [
        'multiple' => true,
        'accept' => 'image/*'
    ],
    'pluginOptions' => [
        'initialPreview' => $initialPreview,
        'initialPreviewAsData' => true,
        'overwriteInitial' => false,
        'uploadUrl' => Url::to(['/upload/file', 'name' => 'CarManagement']),
        'deleteUrl' => Url::to(['/upload/delete']),
        'maxFileCount' => 10,
        'showRemove' => false,
        'showClose' => false,
        'dropZoneEnabled' => false,
        'initialPreviewConfig' => $initialPreviewConfig,
    ],
    'pluginEvents' => [
        "filepredelete" => "function(event, key, jqXHR, data) {
            var url = $('#carmanagement-delete_driving_license').val();
            if(url==''){
                url = key;
            }else{
                url = url+','+key;
            }
            $('#carmanagement-delete_driving_license').val(url);
        }",
        "filesuccessremove" => "function(event, key) {
            console.log(event);
            console.log(key);
        }",
        "fileuploaded" => "function (event, data, id, index) {
            if(data.response.status)
            {
                var url = $('#carmanagement-driving_license').val();
                if(url==''){
                    url = data.response.url;
                }else{
                    url = url+','+data.response.url;
                }
                $('#carmanagement-driving_license').val(url);
            }
        }",

    ],
];

?>

<div class="car-management-form">

    <?php $form = ActiveForm::begin([
        'options' => ['class' => 'form-horizontal'],
        'fieldConfig' => [
            'template' => "{label}<div class='col-sm-8'>{input}</div>{hint}",
            'labelOptions' => ['class' => 'control-label col-sm-2']
        ]]);
    ?>
    <input type="hidden" id="carmanagement-brand_name" name="CarManagement[brand_name]"
           value="<?= $carManagementModel->brand_name ?>">
    <input type="hidden" id="carmanagement-factory_name" name="CarManagement[factory_name]"
           value="<?= $carManagementModel->factory_name ?>">
    <input type="hidden" id="carmanagement-series_name" name="CarManagement[series_name]"
           value="<?= $carManagementModel->series_name ?>">
    <input type="hidden" id="carmanagement-model_name" name="CarManagement[model_name]"
           value="<?= $carManagementModel->model_name ?>">

    <?= $form->field($carManagementModel, 'driving_license')->hiddenInput()->label(false) ?>
    <?= $form->field($carManagementModel, 'delete_driving_license')->hiddenInput()->label(false) ?>

    <?= $form->field($model, 'terminal_no')->textInput() ?>

    <?= $form->field($carManagementModel, 'brand_id')->dropDownList(CheLogic::instance()->brand()) ?>

    <?= $form->field($carManagementModel, 'factory_id')->dropDownList($factoryItems) ?>

    <?= $form->field($carManagementModel, 'series_id')->dropDownList($seriesItems) ?>

    <?= $form->field($carManagementModel, 'model_id')->dropDownList($modelItems) ?>

    <?= $form->field($carManagementModel, 'plate_number')->textInput(['maxlength' => true]) ?>

    <?= $form->field($carManagementModel, 'frame_number')->textInput(['maxlength' => true]) ?>

    <?= $form->field($carManagementModel, 'file[]')->widget(FileInput::className(), $fileArray)->label('行驶证') ?>

    <div class="form-group">
        <?= Html::button('提交', ['class' => 'btn btn-primary mark_submit center']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
<script type="text/javascript">
    function setAttNameVal(name) {
        $('#carmanagement-' + name + "_name").val($('#carmanagement-' + name + "_id").find("option:selected").text());
    }

    // 设置字段的HTML，以及name值
    function setAttHtml(name, html) {
        $("#carmanagement-" + name + "_id").html(html);
        setAttNameVal('name');
    }

    $(function ($) {
        $('body').delegate("#carmanagement-brand_id", 'change', function () {
            var brandId = $("#carmanagement-brand_id").val();
            setAttNameVal('brand');
            $.get("<?=Url::to(['car/factory'])?>", {brandId: brandId}, function (html) {
                setAttHtml('factory', html);
                setAttHtml('series', '<option>请先选择厂商</option>');
                setAttHtml('model', '<option>请先选择车系</option>');
            })
        });

        $('body').delegate("#carmanagement-factory_id", 'change', function () {
            var brandId = $("#carmanagement-brand_id").val();
            var factoryId = $("#carmanagement-factory_id").val();
            setAttNameVal('factory');
            $.get("<?=Url::to(['car/series'])?>", {brandId: brandId, factoryId: factoryId}, function (html) {
                setAttHtml('series', html);
                setAttHtml('model', '<option>请先选择车系</option>');
            })
        });

        $('body').delegate("#carmanagement-series_id", 'change', function () {
            var seriesId = $("#carmanagement-series_id").val();
            setAttNameVal('series');
            $.get("<?=Url::to(['car/model'])?>", {seriesId: seriesId}, function (html) {
                setAttHtml('model', html);
            })
        });

        $('body').delegate("#carmanagement-model_id", 'change', function () {
            setAttNameVal('model');
        });

        $('.car-management-form').delegate('.mark_submit', 'click', function () {
            $(this).prop("disabled", true);
            var url = $('form').attr('action');
            $.post(url, $('form').serialize(), function (result) {
                if (result.status) {
                    layer.closeAll();
                    location.reload();
                } else {
                    $(this).prop("disabled", false);
                    layer.msg(result.msg, {icon: 2});
                }
            });

        });

    });
</script>
