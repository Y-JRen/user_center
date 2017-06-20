<?php

use common\models\Order;
use passport\helpers\Config;
use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\search\OrderSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '银行待转账记录';
$this->params['breadcrumbs'][] = $this->title;

$this->registerJsFile('/js/web.js', ['depends' => 'yii\web\JqueryAsset']);
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
                        $phone = \common\models\User::findOne($model->uid)->phone;
                        return Html::a($phone, ['/user/order', 'uid' => $model->uid]);
                    }
                ],
                'platform_order_id',
                'order_id',
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
                [
                    'attribute' => 'status',
                    'value' => function ($model) {
                        return $model->orderStatus;
                    },
                    'filter' => Order::getStatusName()
                ],
                'created_at:datetime',
                'updated_at:datetime',
                [
                    'attribute' => 'platform',
                    'value' => function ($model) {
                        return \yii\helpers\ArrayHelper::getValue(Config::getPlatformArray(), $model->platform);
                    },
                ],
                [
                    'label' => '操作',
                    'format' => 'raw',
                    'value' => function ($data) {
                        return Html::button('确认打款', [
                            'data-toggle' => "modal",
                            'data-target' => "#modal",
                            'class' => 'modalClass',
                            'url' => \yii\helpers\Url::to(['/transfer/confirm', 'id' => $data->id])
                        ]);
                    }
                ]
            ],
        ]); ?>
    </div>


    <div class="modal fade" id="modal-default">
        <div class="modal-dialog">
            <div class="modal-content">
            </div>
            <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
    </div>

<?php

$js = <<<_SCRIPT
    $('.modalClass').click(function () {
        $.get($(this).attr('url'),function (html) {
            $('.modal-content').html(html);
            $('#modal-default').modal('show')
        });
    });
_SCRIPT;
$this->registerJs($js);