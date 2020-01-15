<?php

namespace Nails\Common\Common\Form\Field;

/**
 * Class Tel
 *
 * @package Nails\Common\Common\Form\Field
 */
class Tel extends Text
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
        $aData['type'] = 'tel';
        return parent::render($aData);
    }
}
