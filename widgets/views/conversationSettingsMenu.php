<?php

use humhub\modules\mail\models\Message;
use humhub\modules\mail\permissions\StartConversation;
use humhub\widgets\Button;
use humhub\widgets\Link;
use humhub\modules\mail\helpers\Url;
use humhub\widgets\ModalButton;

/* @var $this \humhub\modules\ui\view\components\View */
/* @var $message Message */

if((count($message->users) != 1)) {
    $leaveLinkText = Yii::t('MailModule.views_mail_show', 'Leave conversation');
    $leaveConfirmTitle =  Yii::t('MailModule.views_mail_show', '<strong>Confirm</strong> leaving conversation');
    $leaveConfirmText =  Yii::t('MailModule.views_mail_show', 'Do you really want to leave this conversation?');
    $leaveConfirmButtonText = Yii::t('MailModule.views_mail_show', 'Leave');
} else {
    $leaveLinkText = Yii::t('MailModule.views_mail_show', 'Delete conversation');
    $leaveConfirmTitle = Yii::t('MailModule.views_mail_show', '<strong>Confirm</strong> deleting conversation');
    $leaveConfirmText = Yii::t('MailModule.views_mail_show', 'Do you really want to delete this conversation?');
    $leaveConfirmButtonText = Yii::t('MailModule.views_mail_show', 'Delete');
}

$canStartConversation = Yii::$app->user->can(StartConversation::class);
$isOwn = $message->createdBy->is(Yii::$app->user->getIdentity());
$isOriginator = $message->getUserMessage()->is_originator;
?>

<div class="dropdown">
    <?= Button::primary(Yii::t('custom', '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"><path d="M12 14.25C13.2426 14.25 14.25 13.2426 14.25 12C14.25 10.7574 13.2426 9.75 12 9.75C10.7574 9.75 9.75 10.7574 9.75 12C9.75 13.2426 10.7574 14.25 12 14.25Z" fill="#B4B4B4"/><path d="M19.5 14.25C20.7426 14.25 21.75 13.2426 21.75 12C21.75 10.7574 20.7426 9.75 19.5 9.75C18.2574 9.75 17.25 10.7574 17.25 12C17.25 13.2426 18.2574 14.25 19.5 14.25Z" fill="#B4B4B4"/><path d="M4.5 14.25C5.74264 14.25 6.75 13.2426 6.75 12C6.75 10.7574 5.74264 9.75 4.5 9.75C3.25736 9.75 2.25 10.7574 2.25 12C2.25 13.2426 3.25736 14.25 4.5 14.25Z" fill="#B4B4B4"/></svg>'))
        ->id('conversationSettingsButton')
        ->cssClass('conversation-head-button')
        ->options([
            'data-toggle' => "dropdown",
            'aria-haspopup' => "true",
            'aria-expanded' => "false"
        ])->loader(false)->sm() ?>
    <ul class="dropdown-menu dropdown-right" aria-labelledby="conversationSettingsButton">
        <li>
            <?= ModalButton::none(Yii::t('MailModule.base', 'Tags'))->load(Url::toEditConversationTags($message))->link()->loader(false) ?>
        </li>

        <?php if(($isOwn || Yii::$app->user->isAdmin() || ($isOriginator == 1)) && (count($message->users) > 2)) : ?>
            <li>
                <?= ModalButton::none(Yii::t('MailModule.base', 'Edit title'))->load(Url::toEditConversationTitle($message))->link()->loader(false) ?>
            </li>
        <?php endif; ?>

        <?php if($canStartConversation && ($isOwn || Yii::$app->user->isAdmin() || ($isOriginator == 1)) && (count($message->users) > 2)) : ?>
            <li>
                <?= ModalButton::none(Yii::t('MailModule.views_mail_show', 'Add user'))->load(Url::toAddParticipant($message))->link()->loader(false) ?>
            </li>
        <?php endif; ?>

        <li>
            <?= Link::none($leaveLinkText)->action('mail.conversation.leave', Url::toLeaveConversation($message))->confirm($leaveConfirmTitle, $leaveConfirmText, $leaveConfirmButtonText)->loader(false) ?>
        </li>
    </ul>
</div>
