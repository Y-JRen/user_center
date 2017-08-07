<?php

$item = \backend\logic\MenuLogic::instance(['roleId' => Yii::$app->session->get('ROLE_ID')])->getTree();
array_unshift($item, ['label' => '首页', 'icon' => 'home', 'url' => ['site/index']]);
?>
<aside class="main-sidebar">

    <section class="sidebar">

        <?= dmstr\widgets\Menu::widget(
            [
                'options' => ['class' => 'sidebar-menu'],
                'items' => $item,
            ]
        ) ?>

    </section>

</aside>
