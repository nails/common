<?php

namespace Nails\Common\Common\Form\Field;

/**
 * Class Color
 *
 * @package Nails\Common\Common\Form\Field
 */
class Color extends Text
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
        $aData['type'] = 'color';
        return parent::render($aData);
    }
}
