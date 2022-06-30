<?php
use humhub\libs\Html;
use humhub\modules\ui\filter\widgets\CheckboxFilterInput;
use humhub\modules\ui\form\widgets\ActiveForm;

/* @var $options array */
?>

<?= Html::beginTag('div', $options) ?>
    <?= Html::hiddenInput('unread', '0', [
        'data-filter-id' => 'unread',
        'data-filter-category' => 'unread',
        'data-filter-type' => 'text',
    ]) ?>
    <div class="form-check">
        <label class="form-check-label rocketmailfilter-check-label col-form-label-sm">
            <?= Html::checkbox('unread-toggle', false, [
                'id' => 'rocketmailfilter-unread-toggle',
            ]) ?>
            <?= Yii::t('MailModule.base', 'Show unread only') ?>
        </label>
    </div>
<?= Html::endTag('div') ?>
