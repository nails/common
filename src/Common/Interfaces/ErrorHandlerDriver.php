<?php

/**
 * This interface is implemented by ErrorHandler drivers.
 *
 * @package     Nails
 * @subpackage  common
 * @category    errors
 * @author      Nails Dev Team
 * @link
 */

namespace Nails\Common\Interfaces;

interface ErrorHandlerDriver
{
    public static function init();
    public static function error($errno, $errstr, $errfile, $errline);
    public static function exception($exception);
    public static function fatal();
}
