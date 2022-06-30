<?php

use humhub\modules\mail\helpers\Url;
use humhub\modules\mail\models\forms\AddTag;
use humhub\modules\mail\models\MessageTag;
use humhub\modules\ui\view\components\View;
use humhub\widgets\Button;
use humhub\widgets\GridView;
use humhub\widgets\ModalButton;
use yii\bootstrap\ActiveForm;
use yii\data\ActiveDataProvider;
use yii\grid\ActionColumn;
use yii\helpers\Html;


/* @var $this View */
/* @var $model AddTag */

$dataProvider = new ActiveDataProvider([
    'query' => MessageTag::findByUser(Yii::$app->user->id)
])

?>

<div class="page-tag-manage">
    <div class="layout-content-header">
        <div class="header-desktop">
            <h1><a href="<?= Url::to(['/mail/mail']); ?>"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"><path d="M4.25 12.2744L19.25 12.2744" stroke="#130F26" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path><path d="M10.2998 18.299L4.2498 12.275L10.2998 6.25" stroke="#130F26" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path></svg></a><?= Yii::t('MailModule.base', 'Manage conversation tags') ?></h1>
        </div>
        <div class="header-mobile">
            <div class="mobile-control hidden-from-desktop">
                <div class="control-btn">
                    <a href="<?= Url::to(['/mail/mail']); ?>" class="icon"><svg width="8" height="14" viewBox="0 0 8 14" fill="none" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" clip-rule="evenodd" d="M7 13L1 7L7 1" fill="#F4F4F4"></path><path d="M7 13L1 7L7 1" stroke="#B4B4B4" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path></svg></a>
                </div>
            </div>
            <h1><?= Yii::t('MailModule.base', 'Manage conversation tags') ?></h1>
        </div>
    </div>

    <div class="row">
        <div class="col-xs-12 col-lg-7 layout-content-container">
            <div class="help-block">
                <?= Yii::t('custom', 'Вы можете отметить чат любой меткой — так вы структурируете свои диалоги и не потеряете важный контакт. Метки видны только вам. Вы можете создавать, изменять и удалять метки для любого чата.') ?>
            </div>

            <?php $form = ActiveForm::begin(['action' => Url::toAddTag()]); ?>
            <div class="form-group<?= $model->tag->hasErrors() ? ' has-error' : ''?>" style="margin-bottom:0">
                <div class="input-group">
                    <?= Html::activeTextInput($model->tag, 'name', ['style' => 'height:36px', 'class' => 'form-control', 'placeholder' => Yii::t('MailModule.base', 'Add Tag')]) ?>
                    <span class="input-group-btn">
                        <?= Button::defaultType('<svg width="15" height="14" viewBox="0 0 15 14" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M7.70001 3.85001V9.85001" stroke="#F9754F" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/><path d="M10.7 6.85001H4.70001" stroke="#F9754F" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/><path fill-rule="evenodd" clip-rule="evenodd" d="M10.7317 0.850006H4.9683C2.9593 0.850006 1.70001 2.27194 1.70001 4.28488V9.71513C1.70001 11.7281 2.95344 13.15 4.9683 13.15H10.7317C12.7466 13.15 14 11.7281 14 9.71513V4.28488C14 2.27194 12.7466 0.850006 10.7317 0.850006Z" stroke="#F9754F" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>')->loader()->submit() ?>
                    </span>
                </div>
                <span class="help-block help-block-error">
                    <?= Html::error($model->tag, 'name') ?>
                </span>
            </div>
            <?php ActiveForm::end(); ?>
            <?php $firstRow = true; ?>
            <?= GridView::widget([
                'dataProvider' => $dataProvider,
                'options' => ['class' => 'grid-view'],
                'showHeader' => false,
                'summary' => false,
                'columns' => [
                    'name',
                    [
                        'class' => ActionColumn::class,
                        'buttons' => [
                            'update' => function ($url, $model) {
                                /* @var $model \humhub\modules\topic\models\Topic */
                                return ModalButton::primary('<svg width="14" height="14" viewBox="0 0 14 14" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M8.16493 12.6286H13" stroke="#747474" stroke-width="1.25" stroke-linecap="round" stroke-linejoin="round"/><path fill-rule="evenodd" clip-rule="evenodd" d="M7.52001 1.52986C8.0371 0.911858 8.96666 0.821237 9.59748 1.32782C9.63236 1.35531 10.753 2.22586 10.753 2.22586C11.446 2.64479 11.6613 3.5354 11.2329 4.21506C11.2102 4.25146 4.87463 12.1763 4.87463 12.1763C4.66385 12.4393 4.34389 12.5945 4.00194 12.5982L1.57569 12.6287L1.02902 10.3149C0.952442 9.98953 1.02902 9.64785 1.2398 9.3849L7.52001 1.52986Z" stroke="#747474" stroke-width="1.25" stroke-linecap="round" stroke-linejoin="round"/><path d="M6.34721 3.00058L9.98204 5.792" stroke="#747474" stroke-width="1.25" stroke-linecap="round" stroke-linejoin="round"/></svg>')->load(Url::toEditTag($model->id))->loader(false);
                            },
                            'view' => function() {
                                return '';
                            },
                            'delete' => function ($url, $model) {
                                /* @var $model \humhub\modules\topic\models\Topic */
                                return Button::danger('<svg width="14" height="14" viewBox="0 0 14 14" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M11.8833 5.31213C11.8833 5.31213 11.5213 9.80213 11.3113 11.6935C11.2113 12.5968 10.6533 13.1261 9.73927 13.1428C7.99994 13.1741 6.25861 13.1761 4.51994 13.1395C3.64061 13.1215 3.09194 12.5855 2.99394 11.6981C2.78261 9.79013 2.42261 5.31213 2.42261 5.31213" stroke="#747474" stroke-width="1.25" stroke-linecap="round" stroke-linejoin="round"/><path d="M12.8055 3.15981H1.50014" stroke="#747474" stroke-width="1.25" stroke-linecap="round" stroke-linejoin="round"/><path d="M10.6271 3.1598C10.1037 3.1598 9.65307 2.7898 9.55041 2.27713L9.38841 1.46647C9.28841 1.09247 8.94974 0.833801 8.56374 0.833801H5.74174C5.35574 0.833801 5.01707 1.09247 4.91707 1.46647L4.75507 2.27713C4.65241 2.7898 4.20174 3.1598 3.67841 3.1598" stroke="#747474" stroke-width="1.25" stroke-linecap="round" stroke-linejoin="round"/></svg>')->action('client.post', Url::toDeleteTag($model->id))->confirm(
                                    Yii::t('MailModule.base', '<strong>Confirm</strong> tag deletion'),
                                    Yii::t('MailModule.base', 'Do you really want to delete this tag?'),
                                    Yii::t('base', 'Delete'))->xs()->loader(false);
                            },
                        ],
                    ],
                ]]) ?>
        </div>
    </div>
</div>
