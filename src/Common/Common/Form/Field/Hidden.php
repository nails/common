<?php

namespace Nails\Common\Common\Form\Field;

/**
 * Class Hidden
 *
 * @package Nails\Common\Common\Form\Field
 */
class Hidden extends Text
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
        $aData['type'] = 'hidden';
        return parent::render($aData);
    }
}
