<?php

use humhub\modules\mail\helpers\Url;
use humhub\modules\user\widgets\Image;
use humhub\widgets\ModalButton;
use humhub\modules\content\widgets\richtext\RichText;
use humhub\modules\mail\widgets\TimeAgo;
use humhub\libs\Html;

/* @var $entry \humhub\modules\mail\models\MessageEntry */
/* @var $badge boolean */

$isOwnMessage = $entry->user->is(Yii::$app->user->getIdentity());
$lastEntryId = $entry->message->lastEntry->id;

?>
<div class="message-reaction">
    <span class="reaction-1" data-action-click="mail.ConversationEntry.reaction.react" data-action-params='{"messageEntryId":"<?= $entry->id ?>","type":"heart"}'>❤️</span>
    <span class="reaction-2" data-action-click="mail.ConversationEntry.reaction.react" data-action-params='{"messageEntryId":"<?= $entry->id ?>","type":"flame"}'>🔥</span>
    <span class="reaction-3" data-action-click="mail.ConversationEntry.reaction.react" data-action-params='{"messageEntryId":"<?= $entry->id ?>","type":"thumb_up"}'>👍</span>
    <span class="reaction-4" data-action-click="mail.ConversationEntry.reaction.react" data-action-params='{"messageEntryId":"<?= $entry->id ?>","type":"crying"}'>😢</span>
</div>
<?php if(($isOwnMessage && ($lastEntryId == $entry->id)) || ($isOwnMessage && Yii::$app->user->isAdmin())) : ?>
    <li class="edit">
        <?= ModalButton::none('' . Yii::t('custom', 'Редактировать') . '')->cssClass('conversation-edit-button')->load(Url::toEditMessageEntry($entry))->link() ?>
    </li>
<?php endif ?>
