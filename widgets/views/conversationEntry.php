<?php

use humhub\modules\mail\helpers\Url;
use humhub\modules\mail\widgets\SentOrSeen;
use humhub\modules\user\widgets\Image;
use humhub\widgets\ModalButton;
use humhub\modules\content\widgets\richtext\RichText;
use humhub\modules\mail\widgets\TimeAgo;
use humhub\libs\Html;
use humhub\modules\rocketcore\widgets\UserOccupation;

/* @var $this \humhub\modules\ui\view\components\View */
/* @var $entry \humhub\modules\mail\models\MessageEntry */
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
?>

<?= Html::beginTag('div', $options) ?>

<?php if(!$isOwnMessage) : ?>
    <div class="item message-reply<?php if ($usersCount == 2) :?> message-personal<?php endif;?>">
        <div class="space-out-h-zero-xs row">
            <div class="space-in-h-zero-xs col-xs-shrink">
                <div class="avatar<?php if($userDisabled) : ?> profile-disable<?php endif;?><?php if ($usersCount == 2) :?> hidden-from-mobile<?php endif;?>">
                    <?= Image::widget(['user' => $entry->user, 'width' => 40, 'showTooltip' => true]) ?>
                </div>
            </div>
            <div class="space-in-h-zero-xs col-xs">
                <div class="content row row-between-lg space-out-h-zero-xs">
                    <?php if ($usersCount > 2) :?>
                        <div class="head col-xs-12 hidden-from-mobile space-in-h-zero-xs">
                            <p<?php if($userDisabled) : ?> class="profile-disable"<?php endif;?>><a href="<?= $entry->user->getUrl()?>"><?= Html::encode($entry->user->displayName);?></a></p>
                        </div>
                    <?php endif;?>
                    <div class="message col-xs-shrink col-lg-12 space-in-h-zero-xs <?= $contentClass ?> ">
                        <?php if ($usersCount > 2) :?>
                            <div class="head hidden-from-desktop">
                                <p<?php if($userDisabled) : ?> class="profile-disable"<?php endif;?>><a href="<?= $entry->user->getUrl()?>"><?= Html::encode($entry->user->displayName); ?></a></p>
                            </div>
                        <?php endif;?>
                        <?= RichText::output($entry->content) ?>
                        <div class="foot hidden-from-desktop">
                            <?= $this->render('_conversationEntryMenu', ['entry' => $entry, 'badge' => false]) ?>
                        </div>
                    </div>
                    <div class="foot col-xs-12 col-lg-shrink col-last-lg hidden-from-mobile space-in-h-zero-xs">
                        <?= $this->render('_conversationEntryMenu', ['entry' => $entry, 'badge' => false]) ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>

<?php if($isOwnMessage) : ?>
    <div class="item message-owner<?php if ($usersCount == 2) :?> message-personal<?php endif;?>">
        <div class="space-out-h-zero-xs row">
            <div class="space-in-h-zero-xs col-xs">
                <div class="content row row-between-lg space-out-h-zero-xs">
                    <div class="message col-xs-shrink col-lg-12 col-first-xs space-in-h-zero-xs <?= $contentClass ?>">
                        <?= RichText::output($entry->content) ?>
                        <div class="foot hidden-from-desktop">
                            <?= $this->render('_conversationEntryMenu', ['entry' => $entry, 'badge' => false]) ?>
                            <?= SentOrSeen::widget(['entry' => $entry])?>
                        </div>
                    </div>
                    <div class="foot col-xs-12 col-lg-8 col-last-lg hidden-from-mobile space-in-h-zero-xs">
                        <?= $this->render('_conversationEntryMenu', ['entry' => $entry, 'badge' => false]) ?>
                        <?= SentOrSeen::widget(['entry' => $entry])?>
                    </div>
                </div>
            </div>
            <div class="space-in-h-zero-xs col-xs-shrink hidden-from-mobile">
                <div class="avatar<?php if($userDisabled) : ?> profile-disable<?php endif;?>">
                    <?= Image::widget(['user' => $userModel, 'link'  => false, 'width' => 40, 'htmlOptions' => ['id' => 'user-account-image',]])?>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>

<?= Html::endTag('div') ?>


