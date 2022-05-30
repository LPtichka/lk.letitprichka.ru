<?php

/* @var $this yii\web\View */
/* @var $user \app\models\User */

$resetLink = Yii::$app->urlManager->createAbsoluteUrl(['main/reset-password', 'token' => $user->password_reset_token]);
?>
    Здравствуйте, <?= $user->fio ?>!

    Перейдите по ссылке ниже, чтобы сбросить пароль:

<?= $resetLink ?>