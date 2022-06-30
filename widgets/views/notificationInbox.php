<?php

use humhub\modules\mail\assets\MailMessengerAsset;
use humhub\modules\mail\assets\MailNotificationAsset;
use humhub\modules\mail\permissions\StartConversation;
use humhub\modules\mail\widgets\NewMessageButton;
use humhub\modules\mail\helpers\Url;

/* @var $this \humhub\modules\ui\view\components\View */

MailNotificationAsset::register($this);

$canStartConversation = Yii::$app->user->can(StartConversation::class);
?>
