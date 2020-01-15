<?php

namespace Nails\Common\Common\Form\Field;

/**
 * Class DateTime
 *
 * @package Nails\Common\Common\Form\Field
 */
class DateTime extends Text
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
        $aData['type'] = 'datetime-local';
        return parent::render($aData);
    }
}
