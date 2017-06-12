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
                            ['label' => '线下充值确认', 'icon' => 'file-code-o', 'url' => ['/order/line-down'],],
                            ['label' => '提现确认', 'icon' => 'file-code-o', 'url' => ['/order/cash'],],
                            ['label' => '贷款进账', 'icon' => 'file-code-o', 'url' => ['/order/loan'],],
                            ['label' => '财务退款', 'icon' => 'file-code-o', 'url' => ['/order/refund'],],
                            ['label' => '消费记录', 'icon' => 'file-code-o', 'url' => ['/order'],],
                        ],
                    ],
                ],

            ]
        ) ?>

    </section>

</aside>
