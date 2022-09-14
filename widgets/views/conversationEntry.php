<?php

use humhub\modules\mail\models\MessageEntry;
use humhub\modules\mail\widgets\ConversationEntryReactions;
use humhub\modules\mail\widgets\SentOrSeen;
use humhub\modules\ui\view\components\View;
use humhub\modules\user\widgets\Image;
use humhub\widgets\ModalButton;
use humhub\modules\content\widgets\richtext\RichText;
use humhub\libs\Html;
use humhub\modules\rocketcore\widgets\UserOccupation;

/* @var $this View */
/* @var $entry MessageEntry */
/* @var $options array */
/* @var $contentClass string */
/* @var $showUserInfo boolean */
/* @var $isOwnMessage boolean */
/* @var $usersCount integer */

$isOwnMessage = $entry->user->is(Yii::$app->user->getIdentity());
$userModel = Yii::$app->user->identity;
$occupation = class_exists('\\humhub\\modules\\rocketcore\\widgets\\UserOccupation')
    ? \humhub\modules\rocketcore\widgets\UserOccupation::widget(['model' => $entry->user])
    : '';
$userDisabled = class_exists('\\humhub\\modules\\musztabel\\widgets\\PattyStatus')
    ? \humhub\modules\musztabel\widgets\PattyStatus::widget(['model' => $entry->user])
    : ''; //Helps to check if user is disabled. Returns 'Deactivated' if user's status is not equal to ENABLED. Can be customized in /views/musztabel/widgets/pattyStatus.php
/** @var MessageEntry $reply */
$reply = $entry->getReply()->one();
?>

<?= Html::beginTag('div', $options) ?>

<?php if(!$isOwnMessage) : ?>
    <div class="item message-reply<?php if ($usersCount == 2) :?> message-personal<?php endif;?>">
        <div class="row row-top-xs space-out-h-zero-xs">
            <div class="col-xs-shrink space-in-h-zero-xs">
                <div class="avatar<?php if($userDisabled) : ?> profile-disable<?php endif;?><?php if ($usersCount == 2) :?> hidden-from-mobile<?php endif;?>">
                    <?= Image::widget(['user' => $entry->user, 'width' => 40, 'showTooltip' => true]) ?>
                </div>
            </div>
            <div class="col-xs space-in-h-zero-xs">
                <div class="content">
                    <div class="message-frame">
                        <div class="row row-end-xs row-bottom-xs space-out-h-zero-xs">
                            <div class="col-xs space-in-h-zero-xs">
                                <?php if ($usersCount > 2) :?>
                                    <div class="col-xs-12 hidden-from-mobile space-in-h-zero-xs">
                                        <div class="head">
                                            <p<?php if($userDisabled) : ?> class="profile-disable"<?php endif;?>><a href="<?= $entry->user->getUrl()?>"><?= Html::encode($entry->user->displayName);?></a></p>
                                        </div>
                                    </div>
                                <?php endif;?>
                                <div class="col-xs-shrink space-in-h-zero-xs">
                                    <div class="message <?= $contentClass ?> ">
                                        <?php if ($usersCount > 2) :?>
                                            <div class="head hidden-from-desktop">
                                                <p<?php if($userDisabled) : ?> class="profile-disable"<?php endif;?>><a href="<?= $entry->user->getUrl()?>"><?= Html::encode($entry->user->displayName); ?></a></p>
                                            </div>
                                        <?php endif;?>
                                        <?php if ($reply) : ?>
                                            <a href="#" data-action-click="mail.reply.scrollToOriginalMessage" data-action-params='{"messageId":<?= $reply->id ?>}'>
                                                <blockquote class="message-reply-item" data-reply-id="<?= $reply->id ?>">
                                                    <?= RichText::previewWithoutQuotes($reply->content, 40); ?>
                                                </blockquote>
                                            </a>
                                        <?php endif; ?>
                                        <?= RichText::output($entry->content) ?>
                                    </div>
                                    <div class="message-reactions">
                                        <?= ConversationEntryReactions::widget(['entry' => $entry]); ?>
                                    </div>
                                </div>
                            </div>
                            <div class="col-xs-shrink space-in-h-zero-xs">
                                <div class="dropdown">
                                    <div id="conversationSettingsButton" class="dropdown-btn" data-toggle="dropdown" aria-expanded="false">
                                        <span>&middot;</span><span>&middot;</span><span>&middot;</span>
                                    </div>
                                    <ul class="dropdown-menu dropdown-left conversation-menu" aria-labelledby="conversationSettingsButton">
                                        <?= $this->render('_conversationEntryMenu', ['entry' => $entry, 'badge' => false]) ?>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <div class="foot col-xs-12 col-last-xs space-in-h-zero-xs">
                            <?= $this->render('_conversationEntryTime', ['entry' => $entry, 'badge' => false]) ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>

<?php if($isOwnMessage) : ?>
    <div class="item message-owner<?php if ($usersCount == 2) :?> message-personal<?php endif;?>">
        <div class="row row-top-xs space-out-h-zero-xs">
            <div class="col-xs space-in-h-zero-xs">
                <div class="content">
                    <div class="message-frame">
                        <div class="row row-end-xs row-bottom-xs space-out-h-zero-xs">
                            <div class="col-xs-shrink space-in-h-zero-xs">
                                <div class="dropdown">
                                    <div id="conversationSettingsButton" class="dropdown-btn" data-toggle="dropdown" aria-expanded="false">
                                        <span>&middot;</span><span>&middot;</span><span>&middot;</span>
                                    </div>
                                    <ul class="dropdown-menu dropdown-right conversation-menu" aria-labelledby="conversationSettingsButton">
                                        <?= $this->render('_conversationEntryMenu', ['entry' => $entry, 'badge' => false]) ?>
                                    </ul>
                                </div>
                            </div>
                            <div class="col-xs space-in-h-zero-xs">
                                <div class="message <?= $contentClass ?>">
                                    <?php if ($reply) : ?>
                                        <a href="#" data-action-click="mail.reply.scrollToOriginalMessage" data-action-params='{"messageId":<?= $reply->id ?>}'>
                                            <blockquote class="message-reply-item" data-reply-id="<?= $reply->id ?>">
                                                <?= RichText::previewWithoutQuotes($reply->content, 40); ?>
                                            </blockquote>
                                        </a>
                                    <?php endif; ?>
                                    <?= RichText::output($entry->content) ?>
                                </div>
                                <div class="message-reactions">
                                    <?= ConversationEntryReactions::widget(['entry' => $entry]); ?>
                                </div>
                            </div>
                        </div>
                        <div class="foot">
                            <?= $this->render('_conversationEntryTime', ['entry' => $entry, 'badge' => false]) ?>
                            <?= SentOrSeen::widget(['entry' => $entry])?>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xs-shrink space-in-h-zero-xs hidden-from-mobile">
                <div class="avatar<?php if($userDisabled) : ?> profile-disable<?php endif;?>">
                    <?= Image::widget(['user' => $userModel, 'link'  => false, 'width' => 40, 'htmlOptions' => ['id' => 'user-account-image',]])?>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>

<?= Html::endTag('div') ?>
