<?php

namespace Nails\Common\Validation\Rule;

use Nails\Common\Service\Database;
use Nails\Common\Validation\AbstractRule;
use Nails\Common\Validation\Context;
use Nails\Factory;

/**
 * Rule: `is_unique[table.column.ignore_id.ignore_column]` — the value must not already
 * exist in `table.column` (optionally ignoring the row where `ignore_column` = `ignore_id`;
 * `ignore_column` defaults to `id`).
 */
class IsUnique extends AbstractRule
{
    public const NAME            = 'is_unique';
    public const DEFAULT_MESSAGE = 'The {field} field is not unique.';

    public function __construct(private ?Database $oDb = null)
    {
    }

    public function apply(mixed $mValue, Context $oContext): bool
    {
        $aParams       = $oContext->getParams('.');
        $sTable        = $aParams[0] ?? null;
        $sColumn       = $aParams[1] ?? null;
        $sIgnoreId     = $aParams[2] ?? null;
        $sIgnoreColumn = $aParams[3] ?? 'id';

        if (empty($sTable) || empty($sColumn)) {
            return false;
        }

        $oDb = $this->oDb ?? Factory::service('Database');
        $oDb->where($sColumn, $mValue);
        if ($sIgnoreId) {
            $oDb->where($sIgnoreColumn . ' !=', $sIgnoreId);
        }

        return $oDb->count_all_results($sTable) === 0;
    }
}
