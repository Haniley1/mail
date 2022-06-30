<?php


use humhub\libs\Html;
use humhub\modules\mail\helpers\Url;
use humhub\modules\mail\models\forms\InboxFilterForm;
use humhub\modules\mail\widgets\ConversationTagPicker;
use humhub\modules\mail\widgets\ManageTagsLink;
use humhub\modules\ui\filter\widgets\PickerFilterInput;
use humhub\modules\ui\filter\widgets\TextFilterInput;
use humhub\modules\ui\form\widgets\ActiveForm;
use humhub\modules\ui\view\components\View;
use humhub\modules\user\widgets\UserPickerField;
use humhub\widgets\Link;


/* @var $this View */
/* @var $options array */
/* @var $model InboxFilterForm# */
?>
<script>
    $(document).on('humhub:ready', function() {
        const $searchField = $('#mail-conversation-overview input.filterInput')
        const $clearButton = $('#mail-conversation-overview button.close')

        $searchField.on('input', function() {
            const value = $searchField.val()
            value.length === 0 ? $clearButton.hide() : $clearButton.show()
        })

        $clearButton.on('click', function() {
            $searchField.val('');
            $searchField.focus()
            $searchField.trigger('keydown')
            $clearButton.hide()
        })

        $searchField.trigger('input')
    })
</script>

<?= Html::beginTag('div', $options) ?>
<?php $filterForm = ActiveForm::begin() ?>
<div class="mail-filter-container">
    <?= Link::none('<span class="icon"><svg width="12" height="12" viewBox="0 0 12 12" fill="none" xmlns="http://www.w3.org/2000/svg"><circle cx="5.8832" cy="5.8832" r="4.49428" stroke="#333333" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/><path d="M9.00903 9.24243L10.771 10.9999" stroke="#333333" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg></span>' . Yii::t('custom', 'Поиск') . '<span class="icon-arrow"><svg width="10" height="6" viewBox="0 0 10 6" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M9.48546 1L5.24273 5.24578L1 1" stroke="#333333" stroke-width="0.909809" stroke-linecap="round" stroke-linejoin="round"/></svg></span>')
        ->id('conversation-filter-link')
        ->href('#mail-filter-menu')
        ->icon('')
        ->options(['data-toggle' => "collapse"])
        ->cssClass('collapsed')
    ?>

    <div id="mail-filter-menu" class="clearfix collapse">

        <div class="find-form">
            <?= TextFilterInput::widget(['id' => 'term', 'category' => 'term', 'options' => ['placeholder' => Yii::t('custom', 'Искать в чатах')]]) ?>
            <button class="close" type="button"><svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 14 14" fill="none"><path d="M8.59701 5.39673L5.40234 8.59139" stroke="#B4B4B4" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path><path d="M8.59797 8.59326L5.40063 5.39526" stroke="#B4B4B4" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path><path fill-rule="evenodd" clip-rule="evenodd" d="M9.88974 0.833496H4.11041C2.09641 0.833496 0.83374 2.2595 0.83374 4.2775V9.72283C0.83374 11.7408 2.09041 13.1668 4.11041 13.1668H9.88907C11.9097 13.1668 13.1671 11.7408 13.1671 9.72283V4.2775C13.1671 2.2595 11.9097 0.833496 9.88974 0.833496Z" stroke="#B4B4B4" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path></svg></button>
        </div>
        <div class="form-group">
            <?= PickerFilterInput::widget([
                'id' => 'participants', 'category' => 'participants',
                'picker' => UserPickerField::class,
                'pickerOptions' => ['name' => 'participants', 'placeholder' => Yii::t('MailModule.base', 'By participants')]]) ?>
        </div>
        <div class="message-tag-filter-group">
            <?= PickerFilterInput::widget([
                'id' => 'tags', 'category' => 'tags',
                'picker' => ConversationTagPicker::class,
                'pickerOptions' => ['id' => 'inbox-tag-picker', 'name' => 'tags', 'placeholder' => Yii::t('MailModule.base', 'Tags'), 'placeholderMore' => Yii::t('MailModule.base', 'By tags')]]) ?>

            <div class="tag-filter-control">
                <?= ManageTagsLink::widget() ?>
            </div>
        </div>
    </div>
</div>
<?php ActiveForm::end() ?>
<?= Html::endTag('div') ?>
