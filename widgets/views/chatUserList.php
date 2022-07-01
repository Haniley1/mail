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
                <?= Button::save(Yii::t('MailModule.base', '<svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 15 15" fill="none"><circle cx="6.84442" cy="6.84442" r="5.99237" stroke="#B4B4B4" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></circle><path d="M11.0122 11.3235L13.3616 13.6667" stroke="#B4B4B4" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path></svg>'))
                    ->action('mail.UserList.filter')
                    ->cssClass('chat-user-list-submit') ?>
                <?= $form->field($filter, 'filter')
                    ->textInput(['placeholder' => Yii::t('MailModule.base', 'Search')])
                    ->label(false)
                    ->error(['style'=>'display: none;']) ?>
                <?= Button::save(Yii::t('MailModule.base', '<svg width="20" height="21" viewBox="0 0 20 21" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M12.3955 8.09473L7.60352 12.8867" stroke="#B4B4B4" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path><path d="M12.3976 12.8898L7.60156 8.09277" stroke="#B4B4B4" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path><path fill-rule="evenodd" clip-rule="evenodd" d="M14.335 1.25H5.66598C2.64498 1.25 0.750977 3.389 0.750977 6.416V14.584C0.750977 17.611 2.63598 19.75 5.66598 19.75H14.334C17.365 19.75 19.251 17.611 19.251 14.584V6.416C19.251 3.389 17.365 1.25 14.335 1.25Z" stroke="#B4B4B4" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path></svg>'))
                    ->action('mail.UserList.clear')
                    ->cssClass('chat-user-list-clear')?>
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
                                        <?= Button::save('<svg width="20" height="21" viewBox="0 0 20 21" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M12.3955 8.09473L7.60352 12.8867" stroke="#B4B4B4" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path><path d="M12.3976 12.8898L7.60156 8.09277" stroke="#B4B4B4" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path><path fill-rule="evenodd" clip-rule="evenodd" d="M14.335 1.25H5.66598C2.64498 1.25 0.750977 3.389 0.750977 6.416V14.584C0.750977 17.611 2.63598 19.75 5.66598 19.75H14.334C17.365 19.75 19.251 17.611 19.251 14.584V6.416C19.251 3.389 17.365 1.25 14.335 1.25Z" stroke="#B4B4B4" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path></svg>')
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


