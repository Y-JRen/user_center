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
        <!-- 头部导航区域 -->
        <div class="navigation">
            <ul>
                <?php $projects = \backend\logic\ThirdLogic::instance()->getUserProjects(Yii::$app->user->id); ?>
                <?php foreach ($projects as $project): ?>
                    <li <?= (stristr($project['name'], '用户中心') === false) ? '' : 'class="active"'; ?>>
                        <a href="<?= $project['url'] ?>" target="_blank"><?= $project['name'] ?></a>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>

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
<style>
    .navigation {
        position: absolute;
        left: 60px;
        width: 69%;
        height: 100%;
    }

    .navigation ul {
        height: 100%;
    }

    .navigation ul li {
        list-style: none;
        width: 136px;
        height: 100%;
        float: left;
        line-height: 48px;
        text-align: center;
        margin-right: 1px;
        background: rgba(51, 51, 51, 0.14);

    }

    .navigation ul .active {
        background: rgba(51, 51, 51, 0.32);
    }

    .navigation ul li:hover {
        background: rgba(51, 51, 51, 0.32);
    }

    .navigation ul li a {
        display: block;
        font-family: PingFangSC-Medium;
        font-size: 14px;
        color: #FFFFFF;
        letter-spacing: 0;
        width: 100%;
        height: 100%
    }
</style>