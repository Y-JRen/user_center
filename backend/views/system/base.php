<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;


/* @var $this yii\web\View */
/* @var $form yii\widgets\ActiveForm */

$this->title = '参数设置';
$this->params['breadcrumbs'][] = $this->title;
?>


<?php $form = ActiveForm::begin([
    'options' => ['class' => 'form-horizontal'],
    'fieldConfig' => [
        'template' => "{label}<div class='col-sm-8'>{input}</div>{hint}\n{error}",
        'labelOptions' => ['class' => 'control-label col-sm-2']
    ]]);
?>

    <div class="box box-info box-body">
        <?php foreach ($data as $key => $value): ?>
            <div class="box-body">
                <div class="form-group">
                    <label class="col-sm-2 control-label"><?= $value->label; ?>：</label>

                    <div class="col-sm-6">
                        <?= Html::textInput("value[$value->key]", $value->value, ['class' => 'form-control']) ?>
                    </div>
                    <div class="col-sm-4 text-left">
                        <div class="hint-block">
                            调用参数【<?= $value->key; ?>】
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
        <div class="box-footer">
            <?= Html::submitButton('提交', ['class' => 'btn btn-primary']) ?>
        </div>
    </div>
<?php ActiveForm::end(); ?>