<?php

namespace Nails\Common\Common\Form\Field;

/**
 * Class Password
 *
 * @package Nails\Common\Common\Form\Field
 */
class Password extends Text
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
        $aData['type'] = 'password';
        return parent::render($aData);
    }
}
