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

    /**
     * Returns the component's settings configuration
     *
     * @return Setting[]
     */
    public function get(): array;
}
