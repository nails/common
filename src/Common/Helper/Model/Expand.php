<?php

namespace Nails\Common\Helper\Model;

use Nails\Common\Helper\Model\Expand\Group;

/**
 * Class Expand
 *
 * @package Nails\Common\Helper\Model
 */
class Expand
{
    /**
     * The expansions trigger word
     *
     * @var string
     */
    private $sTrigger;

    /**
     * The config array for the expansion
     *
     * @var array
     */
    private $aConfig = [];

    // --------------------------------------------------------------------------

    /**
     * Expand constructor.
     *
     * @param string           $sTrigger The trigger to expand
     * @param array|self|Group $aConfig  A config array, or a nested expand group
     */
    public function __construct(string $sTrigger, $aConfig = [])
    {
        $this->sTrigger = $sTrigger;

        if ($aConfig instanceof Group || $aConfig instanceof self) {
            $aConfig = ['expand' => [$aConfig]];
        }

        $this->aConfig = $aConfig;
    }

    // --------------------------------------------------------------------------

    /**
     * Compile the expansion
     *
     * @return array|string
     */
    public function compile()
    {
        if (empty($this->aConfig)) {
            return $this->sTrigger;
        }

        if (empty($this->aConfig['expand'])) {
            $this->aConfig['expand'] = [];
        }

        if ($this->aConfig['expand'] instanceof self || $this->aConfig['expand'] instanceof Expand\Group) {
            $this->aConfig['expand'] = [$this->aConfig['expand']];
        }

        static::compileHelpers(
            $this->aConfig['expand'],
            static::extractHelpers($this->aConfig['expand'])
        );

        return [
            $this->sTrigger,
            $this->aConfig,
        ];
    }

    // --------------------------------------------------------------------------

    /**
     * Helper method to extract helper objects from an array
     *
     * @param array $aArray The array to search
     *
     * @return array
     */
    public static function extractHelpers(array &$aArray)
    {
        $aHelpers = [];
        foreach ($aArray as $sKey => $mItem) {
            if ($mItem instanceof self || $mItem instanceof Expand\Group) {
                $aHelpers[] = $mItem;
                unset($aArray[$sKey]);
            }
        }

        return $aHelpers;
    }

    // --------------------------------------------------------------------------

    /**
     * Compile helpers into a configuration suarray
     *
     * @param array $aArray The configuration array to compile into
     * @param array $aHelpers
     *
     * @return array
     */
    public static function compileHelpers(array &$aArray = [], array $aHelpers = [])
    {
        foreach ($aHelpers as $mTrigger) {
            if ($mTrigger instanceof Expand\Group) {
                $aArray = array_merge($aArray, $mTrigger->compile());
            } elseif ($mTrigger instanceof self) {
                $aArray[] = $mTrigger->compile();
            }
        }

        return $aArray;
    }
}
