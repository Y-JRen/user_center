<?php

use backend\models\Platform;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\Dealer */
/* @var $form yii\widgets\ActiveForm */
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

    <?= $form->field($model, 'platform_dealer_id')->textInput() ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>
</div>

<div class="box-footer">
    <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
</div>

<?php ActiveForm::end(); ?>

