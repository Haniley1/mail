<?php

use humhub\commands\IntegrityController;
use humhub\components\console\Application;
use humhub\components\Widget;
use humhub\modules\mail\Events;
use humhub\modules\mail\widgets\ConversationSettingsMenu;
use humhub\modules\mail\widgets\Messages;
use humhub\modules\user\models\User;
use humhub\widgets\TopMenu;
use humhub\widgets\NotificationArea;
use humhub\modules\user\widgets\ProfileHeaderControls;

return [
    'id' => 'mail',
    'class' => 'humhub\modules\mail\Module',
    'namespace' => 'humhub\modules\mail',
    'events' => [
        ['class' => User::class, 'event' => User::EVENT_BEFORE_DELETE, 'callback' => ['humhub\modules\mail\Events', 'onUserDelete']],
        ['class' => TopMenu::class, 'event' => TopMenu::EVENT_INIT, 'callback' => ['humhub\modules\mail\Events', 'onTopMenuInit']],
        ['class' => NotificationArea::class, 'event' => NotificationArea::EVENT_INIT, 'callback' => ['humhub\modules\mail\Events', 'onNotificationAddonInit']],
        ['class' => ProfileHeaderControls::class, 'event' => ProfileHeaderControls::EVENT_INIT, 'callback' => ['humhub\modules\mail\Events', 'onProfileHeaderControlsInit']],
        ['class' => IntegrityController::class, 'event' => IntegrityController::EVENT_ON_RUN, 'callback' => ['humhub\modules\mail\Events', 'onIntegrityCheck']],
        ['class' => 'humhub\modules\rest\Module', 'event' => 'restApiAddRules', 'callback' => ['humhub\modules\mail\Events', 'onRestApiAddRules']],
        ['class' => Application::class, 'event' => Application::EVENT_BEFORE_ACTION, 'callback' => ['humhub\modules\mail\Events', 'onBeforeConsoleAction']],
        ['class' => ConversationSettingsMenu::class, 'event' => Widget::EVENT_CREATE, 'callback' => [Events::class, 'onConversationSettingsMenu']],
        ['class' => Messages::class, 'event' => Widget::EVENT_CREATE, 'callback' => [Events::class, 'onMessagesInit']],
    ],
];
