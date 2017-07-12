<?php

use common\models\Order;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\widgets\Pjax;
use yii\helpers\ArrayHelper;


$this->title = '账户资金';
$this->params['breadcrumbs'][] = $this->title;

$balance = ArrayHelper::getValue($userModel->balance, 'amount', 0);
$freeze = ArrayHelper::getValue($userModel->freeze, 'amount', 0);
?>
<div class="box-body box-profile">
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
</div>

<div class="callout callout-info" style="margin-bottom: 0!important;">
    以下括号里面的金额，代表负数
</div>

<div class="box-body box-profile">
    <div class="nav-tabs-custom">
        <ul class="nav nav-tabs">
            <li class="active"><a href="#balance" data-toggle="tab" aria-expanded="true">可用余额资金记录</a></li>
            <li class=""><a href="#freeze" data-toggle="tab" aria-expanded="false">冻结余额资金记录</a></li>
        </ul>
        <div class="tab-content">
            <div class="tab-pane active" id="balance">
                <?= GridView::widget([
                    'dataProvider' => $balanceProvider,
                    'columns' => [
                        ['class' => 'yii\grid\SerialColumn'],

                        'id',
                        [
                            'attribute' => 'order_id',
                            'format' => 'raw',
                            'value' => function ($model) {
                                return Html::a($model->order_id, ['/order/detail', 'orderId' => $model->order_id], ['target' => '_blank']);
                            }
                        ],
                        'desc',
                        'before_amount:currency',
                        'amount:currency',
                        'after_amount:currency',
                        'remark',
                    ],
                ]); ?>
            </div>

            <div class="tab-pane" id="freeze">
                <?= GridView::widget([
                    'dataProvider' => $freezeProvider,
                    'columns' => [
                        ['class' => 'yii\grid\SerialColumn'],

                        'id',
                        [
                            'attribute' => 'order_id',
                            'format' => 'raw',
                            'value' => function ($model) {
                                return Html::a($model->order_id, ['/order/detail', 'orderId' => $model->order_id], ['target' => '_blank']);
                            }
                        ],
                        'desc',
                        'before_amount:currency',
                        'amount:currency',
                        'after_amount:currency',
                        'remark',
                    ],
                ]); ?>
            </div>
        </div>
    </div>
</div>