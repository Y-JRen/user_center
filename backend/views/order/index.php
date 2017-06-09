<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
/* @var $this yii\web\View */
/* @var $searchModel backend\models\search\OrderrSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '消费记录';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="order-index">


<?php Pjax::begin(['enablePushState' => false]); ?>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            [
                'attribute' => 'uid',
                'label' => '用户',
                'value' => function($model) {
                    return \common\models\User::findOne($model->uid)->phone;
                }
            ],
            'platform_order_id',
            'order_id',
            [
                'attribute' => 'order_type',
                'value' => function($model) {
                    if ($model->order_type == 1) {
                        $typeName = '充值';
                    } elseif ($model->order_type == 2){
                        $typeName = '消费';
                    } elseif ($model->order_type == 2){
                        $typeName = '退款';
                    } else{
                        $typeName = '提现';
                    }
                    return $typeName;
                },
                'filter' => [
                    1 => '充值',
                    2 => '消费',
                    3 => '退款',
                    4 => '提现',
                ]
            ],
            'order_subtype',
            [
                'attribute' => 'amount',
                'value' => function($model) {
                    return Yii::$app->formatter->asCurrency($model->amount);
                }
            ],
            'status',
            // 'desc',
            // 'notice_status',
            // 'notice_platform_param',
            'created_at:datetime',
            // 'updated_at',
            // 'remark',
            'platform',

            ['class' => 'yii\grid\ActionColumn','template' => '{view}'],
        ],
    ]); ?>
<?php Pjax::end(); ?></div>
