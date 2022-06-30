<?php
namespace  humhub\modules\mail\widgets;

use humhub\modules\content\widgets\richtext\RichText as BaseRichText;
use humhub\modules\mail\widgets\richtext\ProsemirrorRichTextConverter;

abstract class RichText extends BaseRichText
{
    /**
     * @return string
     * @since 1.8
     */
    public static function getConverterClass(): string
    {
        return ProsemirrorRichTextConverter::class;
    }
}
