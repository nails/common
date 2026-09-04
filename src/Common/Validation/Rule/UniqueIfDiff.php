<?php

namespace Nails\Common\Validation\Rule;

use Nails\Common\Service\Database;
use Nails\Common\Validation\AbstractRule;
use Nails\Common\Validation\Context;
use Nails\Factory;

/**
 * Rule: `unique_if_diff[table.column.old_value]` — if the value differs from
 * `old_value` it must not already exist in `table.column`.
 */
class UniqueIfDiff extends AbstractRule
{
    public const NAME            = 'unique_if_diff';
    public const DEFAULT_MESSAGE = 'The {field} field is not unique.';

    public function __construct(private ?Database $oDb = null)
    {
    }

    public function apply(mixed $mValue, Context $oContext): bool
    {
        $aParams = explode('.', (string) $oContext->getParam(), 3);
        $sTable  = $aParams[0] ?? null;
        $sColumn = $aParams[1] ?? null;
        $sOld    = $aParams[2] ?? null;

        if (empty($sTable) || empty($sColumn)) {
            return false;
        } elseif ($mValue == $sOld) {
            return true;
        }

        $oDb = $this->oDb ?? Factory::service('Database');
        $oDb->where($sColumn . ' !=', $sOld);
        $oDb->where($sColumn, $mValue);
        $oDb->limit(1);

        return $oDb->get($sTable)->row() === null;
    }
}
