<?php

/**
 * The class provides a base for the Events class which is provided by modules.
 * DocComment parsing adapted from https://stackoverflow.com/a/22526948/789224
 *
 * @package     Nails
 * @subpackage  module-cms
 * @category    Events
 * @author      Nails Dev Team
 */

namespace Nails\Common\Events;

use Nails\Common\Helper\ArrayHelper;

abstract class Base
{
    /**
     * Stores all the DocComments
     *
     * @var array
     */
    protected static $aDescriptions = [];

    /**
     * Stores all the arguments
     *
     * @var array
     */
    protected static $aArguments = [];

    // --------------------------------------------------------------------------

    /**
     * Parses a class for constants and DocComments
     *
     * @param \ReflectionClass $oReflectionClass
     */
    protected static function parse(\ReflectionClass $oReflectionClass)
    {
        $sContent = file_get_contents($oReflectionClass->getFileName());
        $aTokens  = token_get_all($sContent);

        $sDocComment = null;
        $bIsConst    = false;
        foreach ($aTokens as $aToken) {
            if (count($aToken) <= 1) {
                continue;
            }

            list($iTokenType, $sTokenValue) = $aToken;

            switch ($iTokenType) {
                // Ignored tokens
                case T_WHITESPACE:
                case T_COMMENT:
                    break;

                case T_DOC_COMMENT:
                    $sDocComment = $sTokenValue;
                    break;

                case T_CONST:
                    $bIsConst = true;
                    break;

                case T_STRING:
                    if ($bIsConst) {
                        static::$aDescriptions[$sTokenValue] = self::extractDescription($sDocComment);
                        static::$aArguments[$sTokenValue]    = self::extractArgument($sDocComment);
                    }
                    $sDocComment = null;
                    $bIsConst    = false;
                    break;

                // All other tokens reset the parser
                default:
                    $sDocComment = null;
                    $bIsConst    = false;
                    break;
            }
        }
    }

    // --------------------------------------------------------------------------

    /**
     * Extracts the description element of the DocComment
     *
     * @param string $sDocComment The DocComment
     *
     * @return string
     */
    protected static function extractDescription($sDocComment)
    {
        return implode(' ', static::clean($sDocComment, '/^[^@]/'));
    }

    // --------------------------------------------------------------------------

    /**
     * Extracts the argument element of the DocComment
     *
     * @param string $sDocComment The DocComment
     *
     * @return array
     */
    protected static function extractArgument($sDocComment)
    {
        return array_map(
            function ($sLine) {
                return preg_replace('/^@param /', '', $sLine);
            },
            static::clean($sDocComment, '/^@param/')
        );
    }

    // --------------------------------------------------------------------------

    /**
     * Returns lines from a DocComment which match $sPattern
     *
     * @param string $sDocComment The DocComment
     * @param string $sPattern    The pattern to match
     *
     * @return array
     */
    protected static function clean($sDocComment, $sPattern)
    {
        if ($sDocComment === null) {
            return [];
        }

        $aOut   = [];
        $aLines = preg_split('/\R/', $sDocComment);
        foreach ($aLines as $sLine) {
            $sLine = trim($sLine, "/* \t\x0B\0");
            if ($sLine === '' || !preg_match($sPattern, $sLine)) {
                continue;
            }

            $aOut[] = $sLine;
        }
        return $aOut;
    }

    // --------------------------------------------------------------------------

    /**
     * Returns an array of the available events with supporting information
     *
     * @return array
     */
    public static function info()
    {
        $oReflectionClass = new \ReflectionClass(get_called_class());
        static::parse($oReflectionClass);

        $aOut = [];
        foreach ($oReflectionClass->getConstants() as $sConstant => $sValue) {
            $aOut[$sConstant] = (object) [
                'constant'    => get_called_class() . '::' . $sConstant,
                'value'       => $sValue,
                'description' => ArrayHelper::getFromArray($sConstant, static::$aDescriptions),
                'arguments'   => ArrayHelper::getFromArray($sConstant, static::$aArguments),
            ];
        }

        return $aOut;
    }
}
