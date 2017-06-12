<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\widgets\DetailView;
use backend\models\Order;
use common\models\LogReview;

/* @var $this yii\web\View */
/* @var $model backend\models\Order */

$this->title = $model->order_id;
$this->params['breadcrumbs'][] = ['label' => 'Orders', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="order-view">

    <div class="row">
        <div class="col-sm-6">
            <div class="callout callout-info lead">
                <h4>银行账号信息</h4>
                <?php
                $remark = json_decode($model->remark);
                if (is_array($remark)) {
                    foreach ($remark as $key => $value) {
                        echo "<p>$key : $value</p>";
                    }
                } else {
                    echo '<p>银行账号信息不健全</p>';
                }
                ?>
                <h4>订单充值金额：<code><?= $model->amount ?></code></h4>
            </div>
        </div>

        <div class="col-sm-6">
            <?php $logModel = new LogReview(); ?>
            <?php $form = ActiveForm::begin(); ?>
            <?= $form->field($logModel, 'order_status')->dropDownList(LogReview::getStatus()) ?>
            <?= $form->field($logModel, 'remark')->textarea(['placeholder' => '建议填写，不通过时更是要填写']) ?>

            <div class="form-group">
                <?= Html::submitButton('提交', ['class' => 'btn btn-success']) ?>
            </div>
            <?php ActiveForm::end(); ?>
        </div>
    </div>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'uid',
            'platform_order_id',
            'order_id',
            'order_type',
            'order_subtype',
            'amount:currency',
            [
                'attribute' => 'status',
                'value' => function ($model) {
                    return Order::getStatus($model->status);
                }
            ],
            'desc',
            'notice_status',
            'notice_platform_param',
            'platform',
            'created_at:datetime',
            'updated_at:datetime',
        ],
    ]) ?>

</div>
