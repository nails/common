<?php

namespace Nails\Common\Common\Form\Field;

/**
 * Class Date
 *
 * @package Nails\Common\Common\Form\Field
 */
class Date extends Text
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
        $aData['type'] = 'date';
        return parent::render($aData);
    }
}
