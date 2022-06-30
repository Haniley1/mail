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

<?php if(($isOwnMessage && ($lastEntryId == $entry->id)) || ($isOwnMessage && Yii::$app->user->isAdmin())) : ?>
    <div class="edit">
        <?= ModalButton::none('<span class="icon"><svg width="15" height="15" viewBox="0 0 15 15" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M6.66198 0.858887H4.16932C2.11932 0.858887 0.833984 2.31022 0.833984 4.36489V9.90755C0.833984 11.9622 2.11332 13.4136 4.16932 13.4136H10.052C12.1087 13.4136 13.388 11.9622 13.388 9.90755V7.22222" stroke="#B4B4B4" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/><path fill-rule="evenodd" clip-rule="evenodd" d="M4.88541 6.2805L9.86741 1.2985C10.4881 0.678496 11.4941 0.678496 12.1147 1.2985L12.9261 2.10983C13.5467 2.7305 13.5467 3.73716 12.9261 4.35716L7.92008 9.36316C7.64875 9.6345 7.28075 9.78716 6.89675 9.78716H4.39941L4.46208 7.26716C4.47141 6.8965 4.62275 6.54316 4.88541 6.2805Z" stroke="#B4B4B4" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/><path d="M9.11035 2.06787L12.1544 5.11187" stroke="#B4B4B4" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg></span><span class="name">' . Yii::t('custom', 'Редактировать') . '</span>')->cssClass('conversation-edit-button')->load(Url::toEditMessageEntry($entry))->link() ?>
    </div>
<?php endif ?>

<?=TimeAgo::widget(['timestamp' => $entry->created_at, 'badge' => $badge]) ?>
