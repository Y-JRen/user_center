<aside class="main-sidebar">

    <section class="sidebar">

        <?= dmstr\widgets\Menu::widget(
            [
                'options' => ['class' => 'sidebar-menu'],
                'items' => [
                    [
                        'label' => '用户管理',
                        'icon' => 'bars',
                        'url' => '#',
                        'items' => [
                            ['label' => '用户管理', 'icon' => 'file-code-o', 'url' => ['/user'],],
                        ],
                    ],
                    [
                        'label' => '支付中心',
                        'icon' => 'cc',
                        'url' => '#',
                        'items' => [
                            ['label' => '消费记录', 'icon' => 'file-code-o', 'url' => ['/order'],],
                        ],
                    ],
                ],

            ]
        ) ?>

    </section>

</aside>
