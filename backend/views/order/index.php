<?php

use passport\helpers\Config;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
use backend\models\Order;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\search\OrderSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '消费记录';
$this->params['breadcrumbs'][] = $this->title;

$this->registerJsFile('//cdn.jsdelivr.net/momentjs/latest/moment.min.js', ['depends' => 'yii\web\JqueryAsset']);
$this->registerJsFile('//cdn.jsdelivr.net/bootstrap.daterangepicker/2/daterangepicker.js', ['depends' => 'yii\web\JqueryAsset']);
$this->registerCssFile('//cdn.jsdelivr.net/bootstrap.daterangepicker/2/daterangepicker.css', ['depends' => 'yii\bootstrap\BootstrapAsset']);
?>

    <div class="order-index">

        <?= $this->render('_search', ['model' => $searchModel]) ?>

        <?= GridView::widget([
            'dataProvider' => $dataProvider,
            'columns' => [
                ['class' => 'yii\grid\SerialColumn'],

                [
                    'attribute' => 'uid',
                    'label' => '用户手机号',
                    'value' => function ($model) {
                        return \common\models\User::findOne($model->uid)->phone;
                    }
                ],
                [
                    'attribute' => 'platform_order_id',
                    'label' => '电商平台订单号',
                ],
                [
                    'attribute' => 'order_id',
                    'label' => '用户中心订单号',
                ],
                [
                    'attribute' => 'order_type',
                    'value' => function ($model) {
                        return $model->type;
                    },
                ],
                [
                    'attribute' => 'order_subtype',
                    'value' => function ($model) {
                        return ArrayHelper::getValue(Order::$subTypeName, $model->order_subtype, $model->order_subtype);
                    },
                ],
                [
                    'attribute' => 'amount',
                    'value' => function ($model) {
                        return Yii::$app->formatter->asCurrency($model->amount);
                    },
                ],
                [
                    'attribute' => 'status',
                    'value' => function ($model) {
                        return Order::getStatus($model->status);
                    },
                ],
                [
                    'attribute' => 'platform',
                    'value' => function ($model) {
                        return ArrayHelper::getValue(Config::getPlatformArray(), $model->platform);
                    },
                ],
                [
                    'attribute' => 'created_at',
                    'format' => 'datetime',
                    'filterInputOptions' => ['class' => 'form-control', ]
                ],


                ['class' => 'yii\grid\ActionColumn', 'template' => '{view}'],
            ],
        ]); ?>
    </div>
