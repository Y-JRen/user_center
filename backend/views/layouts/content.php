<?php

use dmstr\widgets\Alert;
use yii\helpers\Html;

?>
<div class="content-wrapper">
    <div class="content">
        <?= Alert::widget() ?>
        <section class="content-header">
            <h1 class="page-title"><?= $this->title ?></h1>
            <?php
            $params = Yii::$app->request->queryParams;

            if (isset(Yii::$app->controller->view->context->history)) {
                if (Yii::$app->controller->view->context->history) {
                    $params['history'] = false;
                    array_unshift($params, Yii::$app->request->pathInfo);
                    // 文字显示历史，超链接连接到不显示历史
                    echo Html::a('显示历史', $params);
                } else {
                    $params['history'] = true;
                    array_unshift($params, Yii::$app->request->pathInfo);
                    echo Html::a('不显示历史', $params);
                }
            }
            ?>
        </section>
        <section class="content-body">
            <?= $content ?>
        </section>
    </div>
</div>

<div class='control-sidebar-bg'></div>