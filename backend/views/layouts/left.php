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
                            ['label' => '订单一览', 'icon' => 'file-code-o', 'url' => ['/order/index'],],
                            ['label' => '线下充值', 'icon' => 'file-code-o', 'url' => ['/order/line-down'],],
                            ['label' => '提现确认', 'icon' => 'file-code-o', 'url' => ['/order/cash'],],
                        ],
                    ],
                ],

            ]
        ) ?>

    </section>

</aside>
