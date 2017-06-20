<?php

use common\models\Order;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\widgets\Pjax;
use yii\helpers\ArrayHelper;


$this->title = '提现申请';
$this->params['breadcrumbs'][] = $this->title;

$balance = ArrayHelper::getValue($userModel->balance, 'amount', 0);
$freeze = ArrayHelper::getValue($userModel->freeze, 'amount', 0);
?>
<div class="order-index">
    <div class="box-body no-padding" style="background-color: #fff;">
        <table class="table">
            <tbody>
            <tr>
                <td><strong>申请人</strong></td>
                <td><?= $model->user->phone ?></td>
                <td><strong>申请时间</strong></td>
                <td><?= Yii::$app->formatter->asDatetime($model->created_at) ?></td>
                <td><strong>提现金额</strong></td>
                <td><i style="color: red"><?= Yii::$app->formatter->asCurrency($model->amount) ?></i></td>
            </tr>
            <tr>
                <td><strong>提现方式</strong></td>
                <td>银行卡</td>
                <td><strong></strong></td>
                <td></td>
                <td><strong></strong></td>
                <td></td>
            </tr>
            </tbody>
        </table>
    </div>
    <p></p>
    <div class="box-body no-padding" style="background-color: #fff;">
        <table class="table">
            <tbody>
            <tr>
                <td><strong>用户ID</strong></td>
                <td><?= $userModel->id ?></td>
                <td><strong>用户手机</strong></td>
                <td><?= $userModel->phone ?></td>
                <td><strong>资金账户状态</strong></td>
                <td>正常</td>
            </tr>
            <tr>
                <td><strong>余额</strong></td>
                <td><?= $balance ?></td>
                <td><strong>可用余额</strong></td>
                <td><?= $balance - $freeze ?></td>
                <td><strong>冻结金额</strong></td>
                <td><?= $freeze ?></td>
            </tr>
            </tbody>
        </table>
    </div>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => isset($searchModel) ? $searchModel : null,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            [
                'attribute' => 'updated_at',
                'label' => '时间',
                'value' => function ($model) {
                    return date('Y-m-d H:i:s', $model->updated_at);
                }
            ],
            [
                'attribute' => 'order_id',
                'format' => 'raw',
                'value' => function ($model) {
                    return \yii\helpers\Html::a($model->order_id, ['/order/view', 'id' => $model->id]);
                }
            ],
            [
                'attribute' => 'order_type',
                'value' => function ($model) {
                    return $model->type;
                },
                'filter' => Order::getTypeName()
            ],
            'order_subtype',
            [
                'attribute' => 'amount',
                'value' => function ($model) {
                    return Yii::$app->formatter->asCurrency($model->amount);
                }
            ],
        ],
    ]); ?>

    <p>
        <?php
        echo Html::a('审批通过', ['/cash/confirm-success', 'id' => $model->id], ['class' => 'btn btn-success', 'data-confirm' => '确认审批通过？', 'data-method' => 'post']);
        echo Html::a('审批不通过', ['/cash/confirm-fail', 'id' => $model->id], ['class' => 'btn btn-danger', 'data-confirm' => '确认审批不通过？', 'data-method' => 'post']);
        ?>
    </p>
</div>
