<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\models\search\OrderSearch */
/* @var $form yii\widgets\ActiveForm */
?>
<div class="box advanced-search-form mb-lg">
    <div class="row">
        <div class="form-group col-lg-4 col-md-6">
            <label for="" class="control-label col-sm-3 t-r">关 键 字：</label>

            <div class="col-sm-9 col-md-9">
                <input class="form-control" type="text" name="key" value="<?= Yii::$app->request->get('key') ?>"
                       placeholder="手机号码">
            </div>
        </div>
        <div class="form-group col-lg-4 col-md-4">
            <label for="" class="control-label col-sm-3 t-r">申请时间：</label>

            <div class="col-sm-9 col-md-9">
                <div class="calender-picker double-time" style="height:34px;padding:5px 15px;">
                    <div class="timeinputbox">
                        <input type="text" id="created_at" name="created_at"
                               value="<?= Yii::$app->request->get('created_at') ?>" placeholder="请输入时间"
                               style="width:100%;padding-left:0;">
                        <i class="glyphicon glyphicon-calendar fa fa-calendar"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="form-group col-md-12">
            <div class="pull-right mr-15">
                <?= Html::submitButton('查询', ['class' => 'btn btn-primary btn-sm pull-left mr-15']) ?>
                <?= Html::resetButton('清除', ['class' => 'btn btn-default btn-sm pull-left mark-clear']) ?>
            </div>
        </div>
    </div>
</div>


