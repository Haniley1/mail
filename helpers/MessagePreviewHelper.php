<?php
namespace  humhub\modules\mail\helpers;

use humhub\modules\mail\models\Message;
use humhub\modules\mail\widgets\RichText;

class MessagePreviewHelper
{
    const MAX_LENGTH = 64;

    public static function stripBlockquotes(Message $message): string
    {
        if(!$message->lastEntry) {
            return 'No message found';
        }

        $parsedText = RichText::preview($message->lastEntry->content, 1024);
        if (mb_strlen($parsedText) > static::MAX_LENGTH) {
            return mb_substr($parsedText, 0, 64) . "...";
        }

        return $parsedText;
    }
}
