<aside class="main-sidebar">

    <section class="sidebar">

        <!-- Sidebar user panel -->
        <div class="user-panel">
            <div class="pull-left image">
                <img src="<?= $directoryAsset ?>/img/user2-160x160.jpg" class="img-circle" alt="User Image"/>
            </div>
            <div class="pull-left info">
                <p><?= \Yii::$app->user->identity ? \Yii::$app->user->identity->fio : ''; ?></p>

                <a href="#"><i class="fa fa-circle text-success"></i> Online</a>
            </div>
        </div>

        <?= dmstr\widgets\Menu::widget(
            [
                'options' => ['class' => 'sidebar-menu tree', 'data-widget' => 'tree'],
                'items'   => [
                    ['label' => 'Меню управления', 'options' => ['class' => 'header']],
                    [
                        'label' => 'Заказы',
                        'icon'  => 'list-ol',
                        'url'   => '#',
                        'items' => [
                            ['label' => 'Список заказов', 'icon' => 'list-ol', 'url' => ['/gii'],],
                            ['label' => 'Покупатели', 'icon' => 'user-o', 'url' => ['/debug'],],
                            ['label' => 'Адреса', 'icon' => 'address-book-o', 'url' => ['/debug'],],
                            ['label' => 'Меню', 'icon' => 'bars', 'url' => ['/debug'],],
                        ],
                    ],
                    [
                        'label' => \Yii::t('app', 'Dishes'),
                        'icon'  => 'cube',
                        'url'   => '#',
                        'items' => [
                            ['label' => \Yii::t('app', 'Dishes'), 'icon' => 'file-code-o', 'url' => ['/dish/index'],],
                            ['label' => \Yii::t('app', 'Exceptions'), 'icon' => 'file-code-o', 'url' => ['/exception/index'],],
                            ['label' => \Yii::t('app', 'Goods'), 'icon' => 'dashboard', 'url' => ['/product/index'],],
                        ],
                    ],
                    [
                        'label' => 'Настройки',
                        'icon'  => 'gears',
                        'url'   => '#',
                        'items' => [
                            ['label' => 'Типы оплат', 'icon' => 'file-code-o', 'url' => ['/payment-type/index'],],
                        ],
                    ],
                    ['label' => \Yii::t('user', 'Users'), 'icon' => 'user-o', 'url' => ['/user/index']],
                ],
            ]
        ) ?>

    </section>

</aside>
