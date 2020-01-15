<?php

/**
 * This service provides an easy way to generate form components
 *
 * @package     Nails
 * @subpackage  common
 * @category    Service
 * @author      Nails Dev Team
 * @link
 */

namespace Nails\Common\Service;

use Nails\Common\Exception\NailsException;
use Nails\Common\Helper\Directory;
use Nails\Common\Interfaces\Form\Field;
use Nails\Components;

/**
 * Class Form
 *
 * @package Nails\Common\Service
 */
class Form
{
    /**
     * The following constants are helpers for the bundled fields
     */
    const FIELD_COLOR    = 'color';
    const FIELD_DATE     = 'date';
    const FIELD_DATETIME = 'datetime';
    const FIELD_EMAIL    = 'email';
    const FIELD_HIDDEN   = 'hidden';
    const FIELD_NUMBER   = 'number';
    const FIELD_PASSWORD = 'password';
    const FIELD_TEL      = 'tel';
    const FIELD_TEXT     = 'text';
    const FIELD_TIME     = 'time';
    const FIELD_URL      = 'url';

    // --------------------------------------------------------------------------

    /**
     * The available form fields
     *
     * @var Field[]
     */
    protected $aFields = [];

    // --------------------------------------------------------------------------

    /**
     * Form constructor.
     */
    public function __construct()
    {
        foreach (Components::available() as $oComponent) {

            $aClasses = $oComponent
                ->findClasses('Common\\Form\\Field')
                ->whichImplement(Field::class);

            foreach ($aClasses as $sClass) {

                $sSlug = strtolower(preg_replace('/.*\\\\Common\\\\Form\\\\Field\\\\/', '', $sClass));

                $this->aFields[$oComponent->slug . '::' . $sSlug] = new $sClass();
            }
        }
    }

    // --------------------------------------------------------------------------

    /**
     * Renders a particular form field
     *
     * @param string $sField     The field to render
     * @param array  $aData      The data to render the field with
     * @param string $sNamespace The namespace of the field
     *
     * @return string
     * @throws NailsException
     */
    public function render(string $sField, array $aData = [], string $sNamespace = 'nails/common'): string
    {
        $sKey = $sNamespace . '::' . $sField;
        if (!array_key_exists($sKey, $this->aFields)) {
            throw new NailsException(
                sprintf(
                    '"%s" is not a valid form field',
                    $sKey
                )
            );
        }

        return $this->aFields[$sKey]->render($aData);
    }
}
