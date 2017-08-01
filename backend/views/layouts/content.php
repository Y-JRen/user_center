<?php

use dmstr\widgets\Alert;
use yii\helpers\Html;

?>
<style type="text/css">

    .btn1 {
        width: 90px;
        height: 22px;
        position: relative;
    }

    .btn1 a {
        width: 90px;
        height: 22px;
    }

    .btn1 button {
        width: 100%;
        height: 100%;
        background: #86d168;
        border: none;
        border-radius: 10px;
        color: #ffffff;
        padding-right: 26px;

    }

    .btn1 span {
        position: absolute;
        display: block;
        width: 20px;
        height: 20px;
        background: #ffffff;
        border-radius: 50%;
        top: 2px;
        right: 1px
    }

    .btn1 .btn_init {
        background: #e6e6e6;
        padding-left: 4px;
        padding-right: 0px;
    }

    .btn1 .span_init {
        left: 1px;
        top: 1px;
    }

    button {
        outline: none;
    }


</style>
<div class="content-wrapper">
    <div class="content">
        <?= Alert::widget() ?>
        <section class="content-header">
            <h1 class="page-title"><?= $this->title ?></h1>
            <?php
            $params = Yii::$app->request->queryParams;

            if (isset(Yii::$app->controller->view->context->history)) {
                echo '<div class="btn1">';
                if (Yii::$app->controller->view->context->history) {
                    $params['history'] = false;
                    array_unshift($params, '/' . Yii::$app->request->pathInfo);
                    // 文字显示历史，超链接连接到不显示历史
                    echo Html::a('<button>显示历史</button><span></span>', $params);
                } else {
                    $params['history'] = true;
                    array_unshift($params, '/' . Yii::$app->request->pathInfo);
                    echo Html::a('<button class="btn_init">不显示</button><span class="span_init"></span>', $params);
                }
                echo '</div>';
            }
            ?>
        </section>
        <section class="content-body">
            <?= $content ?>
        </section>
    </div>
</div>

<div class='control-sidebar-bg'></div>