<?php

use yii\helpers\Html;

/* @var $this \yii\web\View */
/* @var $content string */
?>

<header class="main-header">

    <?= Html::a('<span class="logo-mini">LP</span><span class="logo-lg">' . Yii::$app->name . '</span>', Yii::$app->homeUrl, ['class' => 'logo']) ?>

    <nav class="navbar navbar-static-top" role="navigation">

        <a href="#" class="sidebar-toggle" data-toggle="push-menu" role="button">
            <span class="sr-only">Toggle navigation</span>
        </a>

        <div class="navbar-custom-menu">

            <ul class="nav navbar-nav">
                <li class="dropdown user user-menu">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                        <img src="/images/avatar.png" class="user-image" alt="User Image"/>
                        <span class="hidden-xs"><?= \Yii::$app->user->identity ? \Yii::$app->user->identity->fio : '' ?></span>
                    </a>
                    <ul class="dropdown-menu">
                        <!-- User image -->
                        <li class="user-header">
                            <img src="/images/avatar.png" class="img-circle"
                                 alt="User Image"/>

                            <p>
                                <?= \Yii::$app->user->identity ? \Yii::$app->user->identity->fio : ''; ?>
                                - <?= \Yii::$app->user->identity ? \Yii::$app->user->identity->role : ''; ?>
                                <small><?= \Yii::t('app', 'Registered from'); ?> <?= date('d.m.Y', \Yii::$app->user->identity ? \Yii::$app->user->identity->created_at : 0); ?></small>
                            </p>
                        </li>
                        <!-- Menu Footer-->
                        <li class="user-footer">
                            <div class="pull-left">
                                <a href="<?= \yii\helpers\Url::to(['user/view', 'id' => \Yii::$app->user->getId()]); ?>"
                                   class="btn btn-default btn-flat"
                                   data-href="<?= \yii\helpers\Url::to(['user/view', 'id' => \Yii::$app->user->getId()]); ?>"
                                   data-toggle="modal"
                                   data-target="#modal"
                                >
                                    <?= \Yii::t('app', 'Profile'); ?>
                                </a>
                            </div>
                            <div class="pull-right">
                                <?= Html::beginForm(['/main/logout'], 'post')
                                . Html::submitButton(\Yii::t('app', 'Sign out'), ['class' => 'btn btn-default btn-flat'])
                                . Html::endForm(); ?>
                            </div>
                        </li>
                    </ul>
                </li>
            </ul>
        </div>
    </nav>
</header>
