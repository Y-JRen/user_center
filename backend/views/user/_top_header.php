<?php

use yii\helpers\Html;

$action = Yii::$app->controller->action->id;
?>
<style>
    .content-header {
        display: none
    }
</style>
<section>
    <h1 class="page-title"><a href="<?= Yii::$app->redis->get('returnHistory') ?>">
            <返回
        </a><span>会员详情</span></h1>
</section>
<div class="row mb-md">
    <div class="col-sm-12 col-xs-12 nav-tabs-custom">
        <ul class="nav nav-tabs">
            <li <?= ($action == 'view') ? 'class="active"' : null ?>>
                <?= Html::a('客户信息', ['view', 'uid' => Yii::$app->request->get('uid')]) ?>
            </li>
            <li <?= ($action == 'fund-record') ? 'class="active"' : null ?>>
                <?= Html::a('资金明细', ['fund-record', 'uid' => Yii::$app->request->get('uid')]) ?>
            </li>
            <li <?= ($action == 'order') ? 'class="active"' : null ?>>
                <?= Html::a('订单记录', ['order', 'uid' => Yii::$app->request->get('uid')]) ?>
            </li>
        </ul>
    </div>
</div>