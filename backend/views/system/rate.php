<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

$model = new \common\models\SystemConf();

/* @var $this yii\web\View */
/* @var $model common\models\SystemConf */
/* @var $form yii\widgets\ActiveForm */
?>
<div class="system-conf-update">


    <div class="system-conf-form">

        <?php $form = ActiveForm::begin(); ?>

        <table class="table table-striped table-bordered">
            <thead>
            <tr>
                <th>#</th>
                <th>key</th>
                <th>中文释义</th>
                <th>费率</th>
                <th>封顶上线</th>
                <th>是否显示</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($info as $key => $value): ?>
                <tr>
                    <td></td>
                    <td><input name="type[]" value="<?= $value['type'] ?>"></td>
                    <td><input name="label[]" value="<?= $value['label'] ?>"></td>
                    <td><input name="ratio[]" value="<?= $value['ratio'] ?>">%</td>
                    <td><input name="capped[]" value="<?= $value['capped'] ?>"></td>
                    <td><input name="is_show[]" value="<?= $value['is_show'] ?>"></td>
                </tr>
            <?php endforeach; ?>
            <tr>
                <td></td>
                <td><input name="type[]" value=""></td>
                <td><input name="label[]" value=""></td>
                <td><input name="ratio[]" value="">%</td>
                <td><input name="capped[]" value=""></td>
                <td><input name="is_show[]" value=""></td>
            </tr>
            </tbody>
        </table>

        <div class="form-group">
            <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
        </div>

        <?php ActiveForm::end(); ?>

    </div>

</div>