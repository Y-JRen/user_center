<?php

use common\models\Order;
use yii\grid\GridView;
use yii\widgets\Pjax;
use yii\helpers\ArrayHelper;


$this->title = '账户资金';
$this->params['breadcrumbs'][] = $this->title;

$balance = ArrayHelper::getValue($userModel->balance, 'amount', 0);
$freeze = ArrayHelper::getValue($userModel->freeze, 'amount', 0);
?>
<div class="order-index">
    <div class="box-body no-padding" style="background-color: #fff;">
        <table class="table">
            <tbody>
            <tr>
                <td>用户ID</td>
                <td><?= $userModel->id ?></td>
                <td>用户手机</td>
                <td><?= $userModel->phone ?></td>
                <td>资金账户状态</td>
                <td>正常</td>
            </tr>
            <tr>
                <td>总余额</td>
                <td><?= $balance + $freeze ?></td>
                <td>可用余额</td>
                <td><?= $balance ?></td>
                <td>冻结金额</td>
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
</div>
