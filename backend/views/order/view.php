<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\widgets\DetailView;
use backend\models\Order;

/* @var $this yii\web\View */
/* @var $model backend\models\Order */

$this->title = $model->order_id;
$this->params['breadcrumbs'][] = ['label' => 'Orders', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="order-view">
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
            'remark',
        ],
    ]) ?>


</div>

<div class="row">
    <div class="col-sm-6">
        <div class="order-form">
            <?php $model->scenario = Order::SCENARIO_FINANCE_CONFIRM;// 线下充值确认?>
            <?php $form = ActiveForm::begin(['action' => \yii\helpers\Url::to(['/order/verify'])]); ?>
            <?= $form->field($model, 'status')->dropDownList(Order::getStatus()) ?>
            <div class="form-group">
                <?= Html::submitButton('提交', ['class' => 'btn btn-primary']) ?>
            </div>
            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>
