<?php

namespace Nails\Common\FindReplace\Exclusion;

use Nails\FindReplace\Interfaces\Exclusion;

class Package implements Exclusion
{
    public function hidden(): array
    {
        return [
            NAILS_DB_PREFIX . 'app_setting',
            NAILS_DB_PREFIX . 'migration',
        ];
    }

    public function readOnly(): array
    {
        return [];
    }
}
