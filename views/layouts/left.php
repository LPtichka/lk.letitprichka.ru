<aside class="main-sidebar">

    <section class="sidebar">

        <!-- Sidebar user panel -->
        <div class="user-panel">
            <div class="pull-left image">
                <img src="/images/avatar.png" class="img-circle" alt="User Image"/>
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
                    ['label' => \Yii::t('app', 'Menu'), 'options' => ['class' => 'header']],
                    ['label' => \Yii::t('app', 'Orders'), 'icon' => 'list-ol', 'url' => ['/order/index']],
                    ['label' => \Yii::t('app', 'Subscribes'), 'icon' => 'tasks', 'url' => ['/subscription/index']],
                    ['label' => \Yii::t('app', 'Products'), 'icon' => 'shopping-bag', 'url' => ['/product/index']],
                    ['label' => \Yii::t('app', 'Customers'), 'icon' => 'user-o', 'url' => ['/customer/index']],
                    ['label' => \Yii::t('app', 'Exceptions'), 'icon' => 'list-ul', 'url' => ['/exception/index']],
                    ['label' => \Yii::t('app', 'Addresses'), 'icon' => 'address-book-o', 'url' => ['/address/index']],
                    ['label' => \Yii::t('app', 'Menu'), 'icon' => 'bars', 'url' => ['/menu/index']],
                    ['label' => \Yii::t('dish', 'Dishes'), 'icon' => 'file-code-o', 'url' => ['/dish/index']],
                    ['label' => \Yii::t('app', 'Payment types'), 'icon' => 'money', 'url' => ['/payment-type/index']],
                    ['label' => \Yii::t('app', 'Users'), 'icon' => 'user-o', 'url' => ['/user/index']],
                    ['label' => \Yii::t('app', 'Franchise'), 'icon' => 'briefcase', 'url' => ['/franchise/index']],
                    ['label' => \Yii::t('app', 'Reports'), 'icon' => 'briefcase', 'url' => ['/report/index']],
                    ['label' => \Yii::t('app', 'Settings'), 'icon' => 'cogs', 'url' => ['/setting/index']],
                ],
            ]
        ) ?>

    </section>

</aside>
