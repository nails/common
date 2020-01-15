<?php

namespace Nails\Common\Common\Form\Field;

/**
 * Class Time
 *
 * @package Nails\Common\Common\Form\Field
 */
class Time extends Text
{
    /**
     * Renders the field
     *
     * @param array $aData The data to build the field with
     *
     * @return string
     */
    public function render(array $aData = []): string
    {
        $aData['type'] = 'time';
        return parent::render($aData);
    }
}
