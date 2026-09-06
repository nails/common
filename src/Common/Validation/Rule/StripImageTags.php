<?php

namespace Nails\Common\Validation\Rule;

use Nails\Common\Validation\AbstractRule;
use Nails\Common\Validation\Context;

/**
 * Rule: `strip_image_tags`
 */
class StripImageTags extends AbstractRule
{
    public const NAME            = 'strip_image_tags';
    public const DEFAULT_MESSAGE = 'The {field} field is invalid.';

    public function apply(mixed $mValue, Context $oContext): bool
    {
        if (is_string($mValue)) {
            $oContext->setValue((string) preg_replace(
                [
                    '#<img[\s/]+.*?src\s*=\s*(["\'])([^\\1]+?)\\1.*?\>#i',
                    '#<img[\s/]+.*?src\s*=\s*?(([^\s"\'=<>`]+)).*?\>#i',
                ],
                '\\2',
                $mValue
            ));
        }
        return true;
    }
}
