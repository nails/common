<?php

namespace Nails\Common\FindReplace\Exclusion;

use Nails\FindReplace\Interfaces\Exclusion;

class Package implements Exclusion
{
    public function hidden(): array
    {
        return [
            NAILS_DB_PREFIX . 'app_setting',
        ];
    }

    public function readOnly(): array
    {
        return [];
    }
}
