<?php

use humhub\modules\mail\models\forms\InboxFilterForm;
use humhub\modules\mail\permissions\StartConversation;
use humhub\modules\mail\widgets\ConversationInbox;
use humhub\modules\mail\widgets\FilterUnread;
use humhub\modules\mail\widgets\NewMessageButton;
use humhub\modules\mail\widgets\InboxFilter;
use humhub\modules\ui\icon\widgets\Icon;
use humhub\widgets\Button;
use yii\widgets\LinkPager;

$canStartConversation = Yii::$app->user->can(StartConversation::class);

$filterModel = new InboxFilterForm();

?>

<div id="mail-conversation-overview" class="mail-aside">
    <div class="layout-content-header">
        <div class="header-desktop hidden-from-mobile">
            <h1><a data-action-click="mail.inbox.toggleInbox"><?= Yii::t('custom', 'Ваши чаты') ?></a></h1>
        </div>
        <div class="header-mobile hidden-from-desktop mails-header">
            <h1><a data-action-click="mail.inbox.toggleInbox"><?= Yii::t('custom', 'Чаты') ?></a></h1>
        </div>
        <?php if($canStartConversation) : ?>
            <div class="mail-heading">
                <?= NewMessageButton::widget(['label' => Yii::t('custom', 'Групповой чат <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16" fill="none"><path d="M8.00008 5.55176V10.436" stroke="#B4B4B4" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/><path d="M10.4446 7.99392H5.55566" stroke="#B4B4B4" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/><path fill-rule="evenodd" clip-rule="evenodd" d="M11.1235 1.3335H4.87587C2.69809 1.3335 1.33301 2.87489 1.33301 5.05693V10.9434C1.33301 13.1254 2.69174 14.6668 4.87587 14.6668H11.1235C13.3076 14.6668 14.6663 13.1254 14.6663 10.9434V5.05693C14.6663 2.87489 13.3076 1.3335 11.1235 1.3335Z" stroke="#B4B4B4" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>'), 'icon' => false, 'cssClass' => 'btn-secondary'])?>
            </div>
        <?php endif; ?>
    </div>
    <div class="inbox-wrapper inbox-filter">
        <?= InboxFilter::widget(['model' => $filterModel]) ?>
    </div>
    <div class="inbox-wrapper inbox-list">
        <?= ConversationInbox::widget(['filter' => $filterModel]) ?>
    </div>
</div>

<?= FilterUnread::widget(); ?>
