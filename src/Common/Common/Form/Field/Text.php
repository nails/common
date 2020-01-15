<?php

namespace Nails\Common\Common\Form\Field;

use Nails\Common\Interfaces\Form\Field;

/**
 * Class Text
 *
 * @package Nails\Common\Common\Form\Field
 */
class Text implements Field
{
    /**
     * Merges the supplied data with the field defaults
     *
     * @param array $aData     The data to merge
     * @param array $aDefaults The defaults
     *
     * @return array
     */
    protected function mergeDefaults(array $aData, array $aDefaults): array
    {
        return array_merge(
            $aDefaults,
            $aData
        );
    }

    // --------------------------------------------------------------------------

    /**
     * Flattens the supplied data down into key value pairs
     *
     * @param array $aData the data to flatten
     *
     * @return string
     */
    protected function flattenAttributes(array $aData): string
    {
        $aAttributes = [];
        foreach ($aData as $sKey => $sValue) {
            $aAttributes[] = sprintf('%s="%s"', $sKey, $sValue);
        }

        return implode(' ', $aAttributes);
    }

    // --------------------------------------------------------------------------

    /**
     * Renders the field
     *
     * @param array $aData The data to build the field with
     *
     * @return string
     */
    public function render(array $aData = []): string
    {
        $aData = $this->mergeDefaults(
            $aData,
            [
                'type'  => 'text',
                'name'  => '',
                'value' => '',
            ]
        );

        return sprintf(
            '<input %s />',
            $this->flattenAttributes($aData)
        );
    }
}
