<?php

namespace Nails\Common\Interfaces\Component;

use Nails\Common\Factory\Model\Field;

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
     * @return Field[]
     */
    public function get(): array;
}
