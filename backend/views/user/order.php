<?php

use backend\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;


$this->title = '会员详情';
?>
<?= $this->render('_top_header'); ?>


<?= GridView::widget([
    'dataProvider' => $dataProvider,
    'columns' => [
        [
            'class' => 'yii\grid\SerialColumn',
            'header' => '序号',
        ],
        'create_time:datetime',
        'platform_order_no',
        'pro_type',
        'pro_name',
        'statusName',
        [
            'class' => 'yii\grid\ActionColumn',
            'template' => '{view}',
            'header' => '操作',
            'buttons' => [
                'view' => function ($url, $model, $key) {
                    return Html::a('查看详情', 'javascript:void(0)', [
                        'data-url' => Url::to(['/order/platform', 'platform_order_id' => $model->platform_order_no]),
                        'class' => 'markOrder']);
                }
            ]
        ],
    ],
]); ?>

<?php $this->beginBlock('javascript') ?>
    <script type="text/javascript">
        $(document).ready(function () {
            $(".markOrder").click(function () {
                var url = $(this).attr('data-url');
                $.get(url, function (html) {
                    layer.open({
                        title: '销售订单详情',
                        area: '800px',
                        shadeClose: true,
                        content: html
                    });
                })
            });
        })
    </script>
<?php $this->endBlock() ?>