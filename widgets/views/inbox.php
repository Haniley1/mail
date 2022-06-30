<?php

use yii\helpers\Url;
use humhub\libs\Html;
use humhub\modules\mail\models\UserMessage;
use humhub\modules\mail\widgets\InboxMessagePreview;
use humhub\modules\ui\view\components\View;

/* @var $this View */
/* @var $options array */
/* @var $userMessages UserMessage[] */

?>

<?= Html::beginTag('ul', $options) ?>
<?php if (empty($userMessages)) : ?>
    <li class="inbox-message placeholder">
        <div class="empty">
            <div class="image">
                <img src="<?= $this->theme->getBaseUrl(); ?>/images/message.svg">
            </div>
            <h2><?= Yii::t('custom', 'Сейчас у вас нет новых чатов 😥') ?></h2>
            <p><?= Yii::t('custom', 'Найдите человека в') ?> <a href="<?= Url::to(['/dashboard/dashboard']); ?>"><?= Yii::t('custom', 'ленте') ?></a> <?= Yii::t('custom', 'или') ?> <a href="<?= Url::to(['/search/search']); ?>"><?= Yii::t('custom', 'поиске') ?></a>. <?= Yii::t('custom', 'Обратите внимание на') ?> <a href="<?= Url::to(['/directory/directory/spaces']); ?>"><?= Yii::t('custom', 'группы') ?></a> — <?= Yii::t('custom', 'там общаются много профильных специалистов') ?>.</p>
            <div class="control">
                <a href="<?= Url::to(['/search/search']); ?>" class="btn btn-primary"><?= Yii::t('custom', 'Найти человека через поиск') ?> <span class="icon"><svg width="17" height="15" viewBox="0 0 17 15" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M16.25 7.72607L1.25 7.72607" stroke="white" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/><path d="M10.2002 1.70149L16.2502 7.72549L10.2002 13.7505" stroke="white" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg></span></a>
            </div>
        </div>
    </li>
<?php else: ?>
    <?php foreach ($userMessages as $userMessage) : ?>
        <?= InboxMessagePreview::widget(['userMessage' => $userMessage]) ?>
    <?php endforeach; ?>
<?php endif; ?>
<?= Html::endTag('ul') ?>
