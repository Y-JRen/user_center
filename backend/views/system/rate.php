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
                    <!--<td><input name="is_show[]" value="--><?//= $value['is_show'] ?><!--"></td>-->
                    <td>
                        <input type="radio" name="is_show[]<?=$key?>" <?php if($value['is_show']=='是'){echo 'checked';}; ?> value="是">是
                        <input type="radio" name="is_show[]<?=$key?>" <?php if($value['is_show']=='否'){echo 'checked';}; ?> value="否">否
                    </td>
                </tr>
            <?php endforeach; ?>
            <tr style="display: none">
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
            <?= Html::submitButton($model->isNewRecord ? '新增卡种 / 修改卡种信息' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
        </div>

        <?php ActiveForm::end(); ?>
        <form action="?r=system/psure" method="post" enctype="multipart/form-data">
            <div>
                默认卡：
                <select name="default_checked">
                    <?php foreach ($info as $key => $value): ?>
                        <option value ="<?=$value['type']?>" <?php if($defaultChecked==$value['type']){echo 'selected';};?>>
                            <?=$value['label']?>
                        </option>
                    <?php endforeach;?>
                </select>
            </div>

            是否可更改手续费：
            <input type="radio" name="is_modify" value="true" <?php if($isModify=='true'){echo 'checked';};?>>是
            <input type="radio" name="is_modify" value="false"  <?php if($isModify=='false'){echo 'checked';};?>>否

            <div>
                <input type="submit" value="确认修改" class="btn btn-success">
            </div>

        </form>

    </div>

</div>