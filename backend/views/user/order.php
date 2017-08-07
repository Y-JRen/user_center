<?php

use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\LinkPager;


$this->title = '会员详情';
?>
<?= $this->render('_top_header'); ?>
    <div class="grid-view">
        <table class="table table-bordered table-hover" style="margin-bottom: 20px;">
            <thead>
            <tr>
                <th>序号</th>
                <th>订单时间</th>
                <th>平台</th>
                <th>平台订单号</th>
                <th>商品类型</th>
                <th>商品名称</th>
                <th>订单状态</th>
                <th>操作</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($orderList as $key => $value): ?>
                <tr>
                    <td><?= $key + 1 ?></td>
                    <td><?= ArrayHelper::getValue($value, 'insertTime') ?></td>
                    <td>电商</td>
                    <td><?= $orderNo = ArrayHelper::getValue($value, 'orderNo') ?></td>
                    <td><?= ArrayHelper::getValue($value, 'typeName') ?></td>
                    <td><?= ArrayHelper::getValue($value, 'name') ?></td>
                    <td><?= ArrayHelper::getValue($value, 'statusName') ?></td>
                    <td><?= Html::a('查看详情', 'javascript:void(0)', [
                            'data-url' => Url::to(['/order/platform', 'platform_order_id' => $orderNo]),
                            'class' => 'markOrder']) ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>

        </table>
        <div class="row text-right">
            <div class="col-xs-12"><?= LinkPager::widget(['pagination' => $pagination]); ?></div>
        </div>
    </div>
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