<?php
use yii\helpers\Html;

/* @var $this \yii\web\View */
/* @var $content string */
?>

<header class="main-header">

    <?= Html::a('<span class="logo-mini">后台</span><span class="logo-lg">' . Yii::$app->name . '</span>', Yii::$app->homeUrl, ['class' => 'logo']) ?>

    <nav class="navbar navbar-static-top" role="navigation">

        <a href="#" class="sidebar-toggle" data-toggle="offcanvas" role="button">
            <span class="sr-only">Toggle navigation</span>
        </a>

        <div class="navbar-custom-menu">
            <?= Html::a(
                '<i class="fa fa-fw fa-power-off"></i> <span class="font-sm">退出</span>',
                ['/site/logout'],
                ['data-method' => 'post', 'class' => 'btn btn-default btn-flat']
            )
            ?>
        </div>
    </nav>
</header>
