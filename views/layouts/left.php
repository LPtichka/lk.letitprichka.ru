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
                    ['label' => \Yii::t('app', 'Orders'), 'icon' => 'list-ol', 'url' => ['/order/index']],
                    ['label' => \Yii::t('app', 'Customers'), 'icon' => 'user-o', 'url' => ['/customer/index']],
                    ['label' => \Yii::t('app', 'Addresses'), 'icon' => 'address-book-o', 'url' => ['/address/index']],
                    ['label' => \Yii::t('app', 'Menu'), 'icon' => 'bars', 'url' => ['/menu/index']],
                    ['label' => \Yii::t('app', 'Dishes'), 'icon' => 'file-code-o', 'url' => ['/dish/index']],
                    ['label' => \Yii::t('app', 'Exceptions'), 'icon' => 'file-code-o', 'url' => ['/exception/index']],
                    ['label' => \Yii::t('app', 'Goods'), 'icon' => 'dashboard', 'url' => ['/product/index']],
                    ['label' => 'Типы оплат', 'icon' => 'file-code-o', 'url' => ['/payment-type/index']],
                    ['label' => 'Подписки', 'icon'  => 'list-ol', 'url'   => ['/subscription/index']],
                    ['label' => \Yii::t('user', 'Users'), 'icon' => 'user-o', 'url' => ['/user/index']],
                    ['label' => \Yii::t('app', 'Franchise'), 'icon' => 'user-o', 'url' => ['/franchise/index']],
                ],
            ]
        ) ?>

    </section>

</aside>
