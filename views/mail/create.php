<?php

use humhub\libs\Html;
use humhub\modules\mail\models\forms\CreateMessage;
use humhub\modules\mail\widgets\ConversationTagPicker;
use humhub\modules\mail\widgets\MailRichtextEditor;
use humhub\modules\user\widgets\UserPickerField;
use humhub\widgets\ModalDialog;
use humhub\modules\mail\helpers\Url;
use yii\widgets\ActiveForm;
use humhub\widgets\ModalButton;
use humhub\modules\content\widgets\richtext\ProsemirrorRichTextEditor;

/* @var $model CreateMessage */
$isAdmin = Yii::$app->user->isAdmin();
?>

<?php ModalDialog::begin(['closable' => false]) ?>
<div class="modal-content">
    <?php $form = ActiveForm::begin(['enableClientValidation' => false]); ?>

    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"><path d="M13.5909 12.0002L18.0441 7.54712C18.2554 7.33615 18.3743 7.04986 18.3745 6.75124C18.3748 6.45261 18.2564 6.16612 18.0455 5.95477C17.8345 5.74343 17.5482 5.62455 17.2496 5.62429C16.951 5.62402 16.6645 5.7424 16.4531 5.95337L12 10.4065L7.54687 5.95337C7.33553 5.74202 7.04888 5.62329 6.75 5.62329C6.45111 5.62329 6.16447 5.74202 5.95312 5.95337C5.74178 6.16471 5.62305 6.45136 5.62305 6.75024C5.62305 7.04913 5.74178 7.33577 5.95312 7.54712L10.4062 12.0002L5.95312 16.4534C5.74178 16.6647 5.62305 16.9514 5.62305 17.2502C5.62305 17.5491 5.74178 17.8358 5.95312 18.0471C6.16447 18.2585 6.45111 18.3772 6.75 18.3772C7.04888 18.3772 7.33553 18.2585 7.54687 18.0471L12 13.594L16.4531 18.0471C16.6645 18.2585 16.9511 18.3772 17.25 18.3772C17.5489 18.3772 17.8355 18.2585 18.0469 18.0471C18.2582 17.8358 18.3769 17.5491 18.3769 17.2502C18.3769 16.9514 18.2582 16.6647 18.0469 16.4534L13.5909 12.0002Z" fill="#B4B4B4"></path></svg></button>
        <h4 id="myModalLabel" class="modal-title">
            <?= Yii::t('MailModule.views_mail_create', '<strong>New</strong> message') ?>
        </h4>
    </div>

    <div class="modal-body">

        <?php
        if ($isAdmin) {
            echo $form->field($model, 'recipient')->widget(UserPickerField::class,
                [
                    'url' => Url::toSearchNewParticipants(),
                    'placeholder' => Yii::t('MailModule.views_mail_create', 'Add recipients'),
                ]
            )->label(false);
            echo $form->field($model, 'title')->textInput(['placeholder' => Yii::t('MailModule.base', 'Chat title')])->label(false);
        } else {
            echo $form->field($model, 'recipient')->widget(UserPickerField::class,
                [
                    'url' => Url::toSearchNewParticipants(),
                    'placeholder' => Yii::t('MailModule.forms_InviteRecipientForm', 'Recipient'),
                ]
            )->label(false);
            echo $form->field($model, 'title')->textInput(['placeholder' => Yii::t('MailModule.base', 'Chat title')])->label(false);
        }
        ?>

        <?= $form->field($model, 'message')->widget(
            MailRichtextEditor::class)->label(false) ?>

        <?php /* $form->field($model, 'tags')->widget(ConversationTagPicker::class, ['addOptions' => true]) */?>

    </div>
    <div class="modal-footer">

        <?= ModalButton::submitModal(Url::toCreateConversation(), Yii::t('MailModule.views_mail_create', 'Send'))?>
        <?= ModalButton::cancel()?>

    </div>

    <?php ActiveForm::end(); ?>
</div>

<?php ModalDialog::end() ?>

<?php
if ($isAdmin) {
    print Html::script(' $(\'#recipient\').focus();');
} else {
    print Html::script(' $(\'#message\').focus();');
}
?>
