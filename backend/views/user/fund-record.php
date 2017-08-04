<?php

use backend\grid\FilterColumn;
use backend\grid\GridView;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use backend\models\Order;
use yii\widgets\ActiveForm;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $model common\models\User */

$this->title = '会员详情';
$this->params['breadcrumbs'][] = ['label' => '用户管理', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="row mb-md">
    <div class="col-sm-12 col-xs-12 nav-tabs-custom">
        <ul class="nav nav-tabs">
            <li><a href="<?= Url::to(['user/view', 'uid' => $uid]) ?>">客户信息</a></li>
            <li class="active"><a href="<?= Url::to(['user/fund-record', 'uid' => $uid]) ?>">资金明细</a></li>
            <li><?= Html::a('订单记录', ['order', 'uid' => $uid]) ?></li>
        </ul>
    </div>
</div>


<?php Pjax::begin(); ?>

<?php $form = ActiveForm::begin([
    'action' => ["fund-record?uid=$uid"],
    'method' => 'get',
]); ?>


<?= GridView::widget([
    'dataProvider' => $dataProvider,
    'columns' => [
        [
            'class' => 'yii\grid\SerialColumn',
            'header' => '序号'
        ],
        [
            'attribute' => 'created_at',
            'format' => 'datetime',
            'enableSorting' => true
        ],
        [
            'attribute' => 'order_id',
        ],

        [
            'class' => FilterColumn::className(),
            'attribute' => 'order_type',
            'value' => function ($model) {
                return $model->type;
            },
            'filterArray' => Order::getTypeName(),
        ],
        [
            'class' => FilterColumn::className(),
            'attribute' => 'order_subtype',
            'value' => function ($model) {
                return ArrayHelper::getValue(Order::$subTypeName, $model->order_subtype, $model->order_subtype);
            },
            'filterArray' => Order::$subTypeName
        ],
        'amount:currency',
        [
            'class' => FilterColumn::className(),
            'attribute' => 'orderStatus',
            'filterArray' => Order::getStatusNameCopy(),
        ],
        'desc',

    ],
]); ?>

<?php ActiveForm::end(); ?>
<?php Pjax::end(); ?>
