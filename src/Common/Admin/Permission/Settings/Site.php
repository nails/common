<?php

namespace Nails\Common\Admin\Permission\Settings;

use Nails\Admin\Interfaces\Permission;

class Site implements Permission
{
    public function label(): string
    {
        return 'Can manage Site settings';
    }

    public function group(): string
    {
        return 'Settings';
    }
}
