<?php
declare(strict_types=1);

namespace humhub\modules\mail\widgets;

use humhub\modules\mail\helpers\Url;
use humhub\modules\mail\models\MessageEntry;
use humhub\modules\mail\models\MessageEntryReaction;
use humhub\widgets\JsWidget;

class ConversationEntryReactions extends JsWidget
{
    const EMOJI = [
        MessageEntryReaction::FLAME => '🔥',
        MessageEntryReaction::THUMB_UP => '👍',
        MessageEntryReaction::CRYING => '😢',
        MessageEntryReaction::HEART => '❤️',
    ];

    public $jsWidget = 'mail.ConversationEntry.reaction';

    public MessageEntry $entry;

    public function run(): string
    {
        $reactions = $this->getReactionsEmoji();

        $this->getView()->registerJsConfig('mail.ConversationEntry.reaction', [
            'messageReactionUrl' => Url::toMessageReaction(),
            'reactions' => self::EMOJI,
        ]);

        return $this->render('conversationEntryReactions', [
            'reactions' => $reactions
        ]);
    }

    private function getReactionsEmoji(): array
    {
        $result = [];
        foreach ($this->entry->getReactionsArray() as $reaction) {
            if (!isset(self::EMOJI[$reaction])) {
                continue;
            }

            $result[] = self::EMOJI[$reaction];
        }

        return $result;
    }
}
