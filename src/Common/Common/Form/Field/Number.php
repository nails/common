<?php

namespace Nails\Common\Common\Form\Field;

use Nails\Common\Helper\ArrayHelper;

/**
 * Class Number
 *
 * @package Nails\Common\Common\Form\Field
 */
class Number extends Text
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
        $aData['step'] = ArrayHelper::get('step', $aData, 'any');
        return parent::render($aData);
    }
}
