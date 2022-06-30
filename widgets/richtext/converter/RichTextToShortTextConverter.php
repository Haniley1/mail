<?php
namespace humhub\modules\mail\widgets\richtext\converter;

use humhub\modules\content\widgets\richtext\converter\RichTextToShortTextConverter as BaseConverter;

class RichTextToShortTextConverter extends BaseConverter
{
    /**
     * @inheritDoc
     */
    protected function renderQuote($block)
    {
        return "";
    }
}
