<?php
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $user \app\models\User */

$resetLink = Yii::$app->urlManager->createAbsoluteUrl(['main/reset-password', 'token' => $user->password_reset_token]);
?>
<div class="password-reset">
    <p>Здравствуйте, <?= Html::encode($user->fio) ?>!</p>

    <p>Перейдите по ссылке ниже, чтобы сбросить пароль: </p>

    <p><?= Html::a(Html::encode($resetLink), $resetLink) ?></p>
</div>