<?php

use backend\models\Order;
use common\helpers\JsonHelper;
use passport\helpers\Config;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\search\OrderSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '消费记录';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="order-index">

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => isset($searchModel) ? $searchModel : null,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            [
                'attribute' => 'uid',
                'label' => '用户',
                'format' => 'raw',
                'value' => function ($model) {
                    $phone = ArrayHelper::getValue($model->user, 'phone');
                    return Html::a($phone, ['/user/order', 'uid' => $model->uid]);
                }
            ],
            'order_id',
            [
                'attribute' => 'order_type',
                'value' => function ($model) {
                    return $model->type;
                },
                'filter' => Order::getTypeName()
            ],
            [
                'attribute' => 'order_subtype',
                'value' => function ($model) {
                    return ArrayHelper::getValue(Order::$subTypeName, $model->order_subtype, $model->order_subtype);
                },
            ],
            [
                'label' => '充值金额',
                'attribute' => 'receipt_amount',
                'format' => 'currency'
            ],
            [
                'label' => '银行名称',
                'value' => function ($model) {
                    return ArrayHelper::getValue(ArrayHelper::getValue(JsonHelper::BankHelper($model->remark), 'bankName'), 'value');
                },
            ],
            [
                'label' => '姓名',
                'value' => function ($model) {
                    return ArrayHelper::getValue(ArrayHelper::getValue(JsonHelper::BankHelper($model->remark), 'accountName'), 'value');
                },
            ],
            [
                'label' => '流水单号',
                'value' => function ($model) {
                    return ArrayHelper::getValue(ArrayHelper::getValue(JsonHelper::BankHelper($model->remark), 'referenceNumber'), 'value');
                },
            ],
            [
                'attribute' => 'status',
                'value' => function ($model) {
                    return Order::getStatus($model->status);
                },
                'filter' => Order::getStatusName()
            ],
            'created_at:datetime',
            [
                'label' => '审核时间',
                'attribute' => 'updated_at',
                'format' => 'datetime'
            ],
            [
                'label' => '操作',
                'format' => 'raw',
                'value' => function ($data) {
                    return Html::a('查看', ['/order/view', 'id' => $data->id], ['class' => 'btn btn-primary modalClass btn-xs']);
                }
            ],
        ],
    ]); ?>
</div>
