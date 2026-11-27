<?php

namespace Nails\Common\Interfaces\Component;

use Nails\Components\Setting;

interface Settings
{
    /**
     * Returns the settings label
     *
     * @return string
     */
    public function getLabel(): string;

    // --------------------------------------------------------------------------

    /**
     * The permission class-string that guards this settings page.
     *
     * A user may access the page if they hold this permission, the
     * settings sudo permission (`Nails\Admin\Admin\Permission\Settings\Manage`),
     * or are a super user.
     *
     * @return class-string
     */
    public function getPermission(): string;

    // --------------------------------------------------------------------------

    /**
     * Returns the component's settings configuration
     *
     * @return Setting[]
     */
    public function get(): array;
}
