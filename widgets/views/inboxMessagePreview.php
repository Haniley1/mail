<?php

/**
 * Shows a  preview of given $userMessage (UserMessage).
 *
 * This can be the notification list or the message navigation
 */

use humhub\modules\mail\widgets\MessagesCounter;
use humhub\modules\mail\widgets\SentOrSeen;
use yii\helpers\Html;
use humhub\modules\mail\widgets\TimeAgo;
use humhub\libs\Helpers;
use humhub\modules\mail\helpers\Url;
use humhub\modules\user\widgets\Image;
use humhub\widgets\Label;
use humhub\modules\mail\helpers\MessagePreviewHelper;


/* @var $userMessage \humhub\modules\mail\models\UserMessage */
/* @var $active bool */

$message = $userMessage->message;
$userCount = $message->getUsers()->count();
if ($userCount == 2) {
    $participant = $message->getLastActiveParticipant();
} else {
    $participant = $message->getLastActiveParticipant(true);
}
$lastEntry = $message->lastEntry;
$users = $message->users;
$isNew = $userMessage->isUnread();
$hasNewReaction = $userMessage->hasNewReaction();

if ($lastEntry) {
    if ($lastEntry->user instanceof \humhub\modules\user\models\User) {
        $userDisabled = class_exists('\\humhub\\modules\\musztabel\\widgets\\PattyStatus')
            ? \humhub\modules\musztabel\widgets\PattyStatus::widget(['model' => $lastEntry->user])
            : ''; //Helps to check if user is disabled. Returns 'Deactivated' if user's status is not equal to ENABLED. Can be customized in /views/musztabel/widgets/pattyStatus.php
    }
}
?>

<?php if ($lastEntry) : ?>
    <li data-message-preview="<?= $message->id ?>" class="messagePreviewEntry entry<?= $isNew || $hasNewReaction ? ' unread' : ''?>" data-action-click="mail.notification.loadMessage" data-action-url="<?= Url::toMessenger($message)?>" data-message-id="<?= $message->id ?>">
        <div class="mail-link">
            <div class="avatar<?php if (($userCount == 2) && $userDisabled) : ?> profile-disable<?php endif;?>">
                <?= Image::widget(['user' => $participant, 'width' => '40', 'link' => false])?>
                <?= Label::danger()->cssClass('new-message-badge')->style((!$isNew && !$hasNewReaction ? 'display:none' : '')) ?>
            </div>

            <div class="content">
                <div class="content-title">
                    <h4 class="media-heading<?php if (($userCount == 2) && $userDisabled) : ?> profile-disable<?php endif;?>">
                        <?php
                        if ($userCount == 2) { // If conversation is between 2 users, then show target user's name
                            foreach ($message->users as $k => $user) {
                                if (!$user->isCurrentUser()) {
                                    print '<span class="inbox-entry-title">' . Html::encode($user->displayName) . '</span>';
                                }
                            }
                        } else {
                            print '<span class="inbox-entry-title"><span class="icon"><svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" clip-rule="evenodd" d="M7.59151 13.207C11.2805 13.207 14.4335 13.766 14.4335 15.999C14.4335 18.232 11.3015 18.807 7.59151 18.807C3.90151 18.807 0.749512 18.253 0.749512 16.019C0.749512 13.785 3.88051 13.207 7.59151 13.207Z" stroke="#130F26" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/><path fill-rule="evenodd" clip-rule="evenodd" d="M7.59157 10.02C5.16957 10.02 3.20557 8.057 3.20557 5.635C3.20557 3.213 5.16957 1.25 7.59157 1.25C10.0126 1.25 11.9766 3.213 11.9766 5.635C11.9856 8.048 10.0356 10.011 7.62257 10.02H7.59157Z" stroke="#130F26" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/><path d="M14.4832 8.88226C16.0842 8.65726 17.3172 7.28326 17.3202 5.62026C17.3202 3.98126 16.1252 2.62126 14.5582 2.36426" stroke="#130F26" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/><path d="M16.5955 12.7324C18.1465 12.9634 19.2295 13.5074 19.2295 14.6274C19.2295 15.3984 18.7195 15.8984 17.8955 16.2114" stroke="#130F26" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg></span>' . Html::encode(Helpers::truncateText($message->title, 75)) . '</span>';
                        }
                        ?>
                    </h4>
                    <div class="media-info">
                        <?= SentOrSeen::widget(['entry' => $lastEntry])?>
                        <?= TimeAgo::widget(['timestamp' => $message->lastEntry->updated_at]) ?>
                    </div>
                </div>
                <div class="content-message">
                    <div class="chat-content">
                        <?php if (($userCount == 2) && $userDisabled) : ?><p class="profile-disable-message hidden-from-mobile"><?= Yii::t('custom', 'Профиль сейчас не используется') ?></p><?php endif;?>
                        <p<?php if (($userCount == 2) && $userDisabled) : ?> class="profile-disable"<?php endif;?>><?= $lastEntry->user->is(Yii::$app->user->getIdentity()) ? Yii::t('MailModule.base', 'You') : Html::encode($lastEntry->user->profile->firstname) ?>: <?= MessagePreviewHelper::stripBlockquotes($message) ?></p>
                    </div>
                    <?= MessagesCounter::widget(['message' => $message])?>
                    <?php if ($hasNewReaction) { ?>
                        <div class="has-new-reaction">
                            <img src="<?= Url::to('/themes/careerum-v2/images/thumbs-up.png'); ?>" alt="New reaction" />
                        </div>
                    <?php } ?>
                </div>
            </div>
        </div>
    </li>
<?php endif; ?>

<?php
$this->registerJsConfig('mail.reply', [
    'text' => [
        'reply' => Yii::t('MailModule.base', 'Reply'),
    ],
]);
