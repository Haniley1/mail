<?php

use humhub\modules\mail\assets\MailMessengerAsset;
use humhub\modules\mail\models\UserMessage;
use humhub\modules\mail\widgets\ConversationView;

/* @var $messageId int */
/* @var $userMessages UserMessage[] */

MailMessengerAsset::register($this);

?>

<div class="page-mail">
    <div class="row">
        <div class="col-xs-12 col-lg-4 col-flex-xs">
            <div class="flex-grow-xs">
                <?= $this->render('_conversation_sidebar') ?>
            </div>
        </div>

        <div class="col-xs-12 col-lg-8 col-flex-xs messages">
            <div class="flex-grow-xs">
                <?= ConversationView::widget(['messageId' => $messageId]) ?>
            </div>
        </div>
    </div>
</div>
