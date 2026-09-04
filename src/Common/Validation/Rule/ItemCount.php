<?php

namespace Nails\Common\Validation\Rule;

use Nails\Common\Exception\ValidationException;
use Nails\Common\Validation\AbstractRule;
use Nails\Common\Validation\Context;
use Nails\Common\Validation\MessageStyle;

/**
 * Rule: `item_count[min,max]` — the array must contain between min and max items;
 * wrap the parameter in parentheses, `item_count[(0,5)]`, for exclusive bounds.
 */
class ItemCount extends AbstractRule
{
    public const NAME            = 'item_count';
    public const DEFAULT_MESSAGE = 'The {field} field contains an invalid number of items.';

    public const MESSAGE_FLOOR   = 'This field requires a minimum selection.';
    public const MESSAGE_CEILING = 'This field accepts a maximum selection.';

    public function acceptsArrays(): bool
    {
        return true;
    }

    public function apply(mixed $mValue, Context $oContext): bool
    {
        if (!is_array($mValue)) {
            return false;
        }

        $sParam   = (string) $oContext->getParam();
        $aParams  = array_map(fn($s) => preg_replace('/[^0-9]/', '', $s), explode(',', $sParam));
        $fFloor   = isset($aParams[0]) && $aParams[0] !== '' ? (float) $aParams[0] : 0;
        $fCeiling = isset($aParams[1]) && $aParams[1] !== '' ? (float) $aParams[1] : INF;

        if (str_starts_with($sParam, '(') && str_ends_with($sParam, ')')) {
            $fFloor++;
            $fCeiling--;
        }

        $bWithField = $oContext->getMessageStyle() === MessageStyle::WITH_FIELD;

        if (count($mValue) < $fFloor) {
            throw new ValidationException(
                $this->line($oContext, 'fv_count_floor', $bWithField) ?: static::MESSAGE_FLOOR
            );
        } elseif (count($mValue) > $fCeiling) {
            throw new ValidationException(
                $this->line($oContext, 'fv_count_ceiling', $bWithField) ?: static::MESSAGE_CEILING
            );
        }

        return true;
    }

    private function line(Context $oContext, string $sKey, bool $bWithField): string
    {
        $sLine = $bWithField
            ? ($oContext->translate($sKey . '_field') ?: $oContext->translate($sKey))
            : ($oContext->translate($sKey) ?: $oContext->translate($sKey . '_field'));

        return str_replace('{field}', $oContext->getLabel(), (string) $sLine);
    }
}
