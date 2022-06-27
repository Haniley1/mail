<?php

use humhub\modules\mail\models\forms\UserFilter;
use humhub\modules\mail\models\Message;
use humhub\modules\musztabel\widgets\PattyStatus;
use humhub\modules\musztabel\widgets\UserFollowButton;
use humhub\modules\musztabel\widgets\UserPlan;
use humhub\modules\rocketcore\widgets\UserOccupation;
use humhub\modules\user\models\User;
use humhub\modules\user\widgets\Image;
use humhub\widgets\Button;
use humhub\widgets\ModalButton;
use humhub\widgets\ModalDialog;
use humhub\widgets\AjaxLinkPager;
use yii\data\Pagination;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var string $title */
/* @var User[] $users  */
/* @var Pagination $pagination */
/* @var UserFilter $filter */
/* @var int $conversationId */
/* @var Message $message */

$occupationExist = class_exists('\\humhub\\modules\\rocketcore\\widgets\\UserOccupation');
/** @var User $originator */
$originator = $message->getOriginator()->one();
?>
<?php ModalDialog::begin(['header' => $title]) ?>
    <?php if ($pagination->pageCount > 1) : ?>
        <div class="user-filter">
            <?php $form = ActiveForm::begin(['id' => 'chat-user-list-form']); ?>

            <div class="filter-field">
                <?= $form->field($filter, 'filter')
                    ->textInput(['placeholder' => Yii::t('MailModule.base', 'Search')])
                    ->label(false)
                    ->error(['style'=>'display: none;']) ?>
                <?= Button::save(Yii::t('MailModule.base', 'Find'))
                    ->action('mail.UserList.filter')->cssClass('btn-secondary chat-user-list-submit') ?>
                <?= Button::save(Yii::t('MailModule.base', 'Clear'))
                    ->action('mail.UserList.clear')
                    ->cssClass('btn-secondary')?>
            </div>

            <?php ActiveForm::end(); ?>
        </div>
    <?php endif; ?>

    <?php if (count($users) === 0): ?>
        <div class="modal-body">
            <p><?= Yii::t('UserModule.base', 'No users found.'); ?></p>
        </div>
    <?php endif; ?>

    <div id="userlist-content">
        <div class="section-members">
            <?php foreach ($users as $user) : ?>
                <?php
                $userDisabled = '';
                if (Yii::$app->getModule('musztabel')) {
                    $userDisabled = class_exists('\humhub\modules\musztabel\widgets\PattyStatus')
                        ? PattyStatus::widget(['model' => $user])
                        : false;
                }
                ?>
                <div class="item">
                    <div class="avatar<?php if ($userDisabled) print ' profile-disable'?>">
                        <span class="media-avatar">
                            <?= Image::widget([
                                'user' => $user,
                                'width' => 50,
                                'htmlOptions' => ['class' => 'media-avatar'],
                                'linkOptions' => ['data-contentcontainer-id' => $user->contentcontainer_id]
                            ]); ?>
                        </span>
                    </div>
                    <div class="content">
                        <div class="head">
                            <div class="title">
                                <h2<?php if ($userDisabled) print ' class="profile-disable"'?>>
                                    <a href="<?= $user->getUrl(); ?>"  data-modal-close="1">
                                        <?= Html::encode($user->displayName); ?>
                                    </a>
                                    <?= UserPlan::widget(['model' => $user]) ?>
                                </h2>
                                <?= $occupationExist ? UserOccupation::widget(['model' => $user]) : '' ?>
                            </div>
                            <div class="control">
                                <?php if (Yii::$app->user->isAdmin() && $user->id !== Yii::$app->user->getIdentity()->getId() && $user->id !== $originator->id) : ?>
                                    <div class="chat-user-list-remove">
                                        <?= Button::save('<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"><path d="M13.5909 12.0002L18.0441 7.54712C18.2554 7.33615 18.3743 7.04986 18.3745 6.75124C18.3748 6.45261 18.2564 6.16612 18.0455 5.95477C17.8345 5.74343 17.5482 5.62455 17.2496 5.62429C16.951 5.62402 16.6645 5.7424 16.4531 5.95337L12 10.4065L7.54687 5.95337C7.33553 5.74202 7.04888 5.62329 6.75 5.62329C6.45111 5.62329 6.16447 5.74202 5.95312 5.95337C5.74178 6.16471 5.62305 6.45136 5.62305 6.75024C5.62305 7.04913 5.74178 7.33577 5.95312 7.54712L10.4062 12.0002L5.95312 16.4534C5.74178 16.6647 5.62305 16.9514 5.62305 17.2502C5.62305 17.5491 5.74178 17.8358 5.95312 18.0471C6.16447 18.2585 6.45111 18.3772 6.75 18.3772C7.04888 18.3772 7.33553 18.2585 7.54687 18.0471L12 13.594L16.4531 18.0471C16.6645 18.2585 16.9511 18.3772 17.25 18.3772C17.5489 18.3772 17.8355 18.2585 18.0469 18.0471C18.2582 17.8358 18.3769 17.5491 18.3769 17.2502C18.3769 16.9514 18.2582 16.6647 18.0469 16.4534L13.5909 12.0002Z" fill="#B4B4B4"></path></svg>')
                                            ->action('mail.UserList.remove')
                                            ->options([
                                                'data-action-confirm' => Yii::t('MailModule.base','Are you sure you want to remove user <strong>{username}</strong> from the chat?', ['username' => Html::encode($user->displayName)]),
                                                'data-action-confirm-header' => Yii::t('MailModule.base', 'Confirm removal of the user from the chat'),
                                                'data-action-confirm-text' => Yii::t('MailModule.base', 'Remove'),
                                                'data-action-cancel-text' => Yii::t('MailModule.base', 'Cancel'),
                                                'data-action-params' => '{"userId":"' . $user->id . '","conversationId":"' . $conversationId . '"}'
                                            ])
                                            ->cssClass('btn-close')
                                            ->title(Yii::t('MailModule.base', 'Remove from chat')) ?>
                                    </div>
                                <?php endif; ?>
                                <?= UserFollowButton::widget([
                                    'user' => $user,
                                    'followOptions' => ['class' => 'btn btn-follow'],
                                    'unfollowOptions' => ['class' => 'btn btn-unfollow'],
                                ]); ?>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <div class="pagination-container">
            <?= AjaxLinkPager::widget(['pagination' => $pagination]); ?>
        </div>
    </div>

    <div class="modal-footer">
        <?= ModalButton::cancel(Yii::t('base', 'Close'))?>
    </div>

<script <?= \humhub\libs\Html::nonce() ?>>
    $('#chat-user-list-form').submit(function (e) {
        e.preventDefault();
        $('.chat-user-list-submit').click();
    });
    $(".modal-body").animate({scrollTop: 0}, 200);
</script>

<?php ModalDialog::end() ?>


