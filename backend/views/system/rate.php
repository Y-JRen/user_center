<?php

use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

$model = new \common\models\SystemConf();

/* @var $this yii\web\View */
/* @var $model common\models\SystemConf */
/* @var $form yii\widgets\ActiveForm */

$this->title = '拉卡拉支付费率设置';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="system-conf-update">


    <div class="system-conf-form">

        <?php $form = ActiveForm::begin(); ?>

        <table class="table table-striped table-bordered">
            <thead>
            <tr>
                <th class="text-center">key</th>
                <th class="text-center">中文释义</th>
                <th class="text-center">费率</th>
                <th class="text-center">封顶上线</th>
                <th class="text-center">是否允许修改手续费</th>
                <th class="text-center">是否显示</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($info as $key => $value): ?>
                <tr>
                    <td class="text-center">
                        <?= Html::textInput('type[]', $value['type'], ['class' => 'form-control']) ?>
                    </td>
                    <td class="text-center">
                        <?= Html::textInput('label[]', $value['label'], ['class' => 'form-control']) ?>
                    </td>
                    <td class="text-center">
                        <div class="form-group">
                            <div class="col-sm-10">
                                <?= Html::textInput('ratio[]', $value['ratio'], ['class' => 'form-control']) ?>
                            </div>
                            <label class="col-sm-2 control-label">%</label>
                        </div>
                    </td>
                    <td class="text-center">
                        <?= Html::textInput('capped[]', $value['capped'], ['class' => 'form-control']) ?>
                    </td>
                    <td class="text-center">
                        <?= Html::radioList("is_modify_rate[{$key}][]", ArrayHelper::getValue($value, 'is_modify_rate'), [1 => ' 是', 0 => '否']) ?>
                    </td>
                    <td class="text-center">
                        <?= Html::radioList("is_show[{$key}][]", $value['is_show'], [1 => ' 是', 0 => '否']) ?>
                    </td>
                </tr>
            <?php endforeach; ?>
            <tr>
                <td class="text-center">
                    <?= Html::textInput('type[]', '', ['class' => 'form-control']) ?>
                </td>
                <td class="text-center">
                    <?= Html::textInput('label[]', '', ['class' => 'form-control']) ?>
                </td>
                <td class="text-center">
                    <div class="form-group">
                        <div class="col-sm-10">
                            <?= Html::textInput('ratio[]', '', ['class' => 'form-control']) ?>
                        </div>
                        <label class="col-sm-2 control-label">%</label>
                    </div>

                </td>
                <td class="text-center">
                    <?= Html::textInput('capped[]', '', ['class' => 'form-control']) ?>
                </td>
                <td class="text-center">
                    <?php $key = count($info);
                    echo Html::radioList("is_modify_rate[{$key}][]", 1, [1 => ' 是', 0 => '否']) ?>
                </td>
                <td class="text-center">
                    <?php $key = count($info);
                    echo Html::radioList("is_show[{$key}][]", 1, [1 => ' 是', 0 => '否']) ?>
                </td>
            </tr>
            </tbody>
        </table>


        <div class="row">
            <div class="form-group">
                <label class="col-sm-2 control-label">默认卡：</label>
                <div>
                    <?= Html::dropDownList('default_checked', $defaultChecked, \yii\helpers\ArrayHelper::map($info, 'type', 'label')) ?>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="form-group">
                <label class="col-sm-2 control-label">是否可更改手续费：</label>
                <div class="col-sm-2">
                    <?= Html::radioList('is_modify', $isModify, [1 => '允许', 0 => '不允许']) ?>
                </div>
            </div>
        </div>

        <div class="row" style="display: none">
            <div class="form-group">
                <label class="col-sm-2 control-label">充值是否允许拆单：</label>
                <div class="col-sm-2">
                    <?= Html::radioList('recharge_is_split', $rechargeIsSplit, [1 => '允许', 0 => '不允许']) ?>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="form-group">
                <label class="col-sm-2 control-label">支付是否允许拆单：</label>
                <div class="col-sm-2">
                    <?= Html::radioList('pay_is_split', $payIsSplit, [1 => '允许', 0 => '不允许']) ?>
                </div>
            </div>
        </div>

        <div class="form-group">
            <div class="col-sm-12">
                <?= Html::submitButton('提交', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
            </div>
        </div>

        <?php ActiveForm::end(); ?>
    </div>

</div>