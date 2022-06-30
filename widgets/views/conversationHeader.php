<?php

use humhub\libs\Html;
use humhub\modules\mail\widgets\ConversationSettingsMenu;
use humhub\modules\mail\widgets\ParticipantImages;
use humhub\modules\mail\widgets\ParticipantUserList;
use humhub\modules\user\widgets\Image;
use humhub\widgets\ModalButton;
use humhub\modules\mail\helpers\Url;

/* @var $message \humhub\modules\mail\models\Message */

// Max items (including show more button) to display, should be > 2
$maxUserImageEntries = 3;

$users = $message->users;
$participantsCount = count($users);
?>

<?= ParticipantImages::widget([
    'message' => $message,
    'maxImageEntries' => $maxUserImageEntries,
    'imagesGroupStyle' => ['class' => 'mail-avatars'],
    'firstImageStyle' => ['class' => 'mail-avatar-first'],
    'firstImageStyleDisabled' => ['class' => 'mail-avatar-first profile-disable'],
    'imagesStyle' => ['class' => 'mail-avatar-other'],
    'imagesStyleDisabled' => ['class' => 'mail-avatar-other profile-disable'],
    'oneImageStyle' => ['class' => 'avatar'],
    'oneImageStyleDisabled' => ['class' => 'avatar profile-disable'],
]); ?>

<div class="mail-info">
    <?= ParticipantUserList::widget([
        'message' => $message,
        'participantsCounterStyle' => ['class' => 'mail-info-participants'],
        'targetStatusStyle' => ['class' => 'mail-info-participants hidden-from-desktop'],
    ]); ?>
</div>

<?php if (!empty($users)) : ?>
    <?= ConversationSettingsMenu::widget(['message' => $message]) ?>
<?php endif; ?>
