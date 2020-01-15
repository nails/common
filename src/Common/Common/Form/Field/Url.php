<?php

namespace Nails\Common\Common\Form\Field;

/**
 * Class Url
 *
 * @package Nails\Common\Common\Form\Field
 */
class Url extends Text
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
        $aData['type'] = 'url';
        return parent::render($aData);
    }
}
