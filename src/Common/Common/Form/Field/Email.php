<?php

namespace Nails\Common\Common\Form\Field;

/**
 * Class Email
 *
 * @package Nails\Common\Common\Form\Field
 */
class Email extends Text
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
        $aData['type'] = 'email';
        return parent::render($aData);
    }
}
