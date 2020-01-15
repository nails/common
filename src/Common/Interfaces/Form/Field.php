<?php

namespace Nails\Common\Interfaces\Form;

use Nails\Common\Form\SourceResponse;

/**
 * Interface Field
 *
 * @package Nails\Common\Interfaces\Form
 */
interface Field
{
    /**
     * Renders the field
     *
     * @param array $aData The data to build the field with
     *
     * @return string
     */
    public function render(array $aData = []): string;
}
