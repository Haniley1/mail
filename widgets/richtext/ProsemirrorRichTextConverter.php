<?php
namespace humhub\modules\mail\widgets\richtext;

use humhub\modules\mail\widgets\richtext\converter\RichTextToShortTextConverter;
use humhub\modules\content\widgets\richtext\ProsemirrorRichTextConverter as BaseRichTextConverter;

/**
 * Converter implementation for richtext ProsemirrorRichText.
 *
 * @package humhub\modules\content\widgets\richtext
 * @since 1.8
 */
class ProsemirrorRichTextConverter extends BaseRichTextConverter
{
    /**
     * @inheritdoc
     */
    public function convertToShortText(string $content, array $options = []): string
    {
        return RichTextToShortTextConverter::process($content, $options);
    }
}
