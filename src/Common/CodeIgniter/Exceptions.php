<?php

/**
 * This class exists purely to route CI type errors to the error handler
 *
 * @package     Nails
 * @subpackage  common
 * @category    errors
 * @author      Nails Dev Team
 * @link
 */

namespace Nails\Common\CodeIgniter;

use CI_Exceptions;
use Nails\Factory;

class Exceptions extends CI_Exceptions
{
    /**
     * Override the show_error method and pass to the Nails ErrorHandler
     *
     * @param  string $sSubject    The error's subject
     * @param  string $sMessage    The error message
     * @param string  $sTemplate   Unused; only there to suppress compatability notification
     * @param int     $iStatusCode Unused; only there to suppress compatability notification
     *
     * @return void
     */
    public function show_error($sSubject, $sMessage = '', $sTemplate = 'error_gene...', $iStatusCode = 500)
    {
        $oErrorHandler = Factory::service('ErrorHandler');;
        $oErrorHandler->showFatalErrorScreen($sSubject, $sMessage);
    }

    // --------------------------------------------------------------------------

    /**
     * Override the show_exception method and pass to the Nails ErrorHandler
     *
     * @param $oException
     */
    public function show_exception($oException)
    {
        $oErrorHandler = Factory::service('ErrorHandler');;
        $oErrorHandler->showFatalErrorScreen($oException->getMessage(), $sMessage);
    }

    // --------------------------------------------------------------------------

    /**
     * Overrides the show_php_error method in order to track errors
     *
     * @param int    $iSeverity
     * @param string $sMessage
     * @param string $sFilepath
     * @param int    $iLine
     *
     * @return string
     */
    public function show_php_error($iSeverity, $sMessage, $sFilepath, $iLine)
    {
        $oErrorHandler = Factory::service('ErrorHandler');;
        $oErrorHandler->triggerError($iSeverity, $sMessage, $sFilepath, $iLine);
    }

    // --------------------------------------------------------------------------

    /**
     * Renders the 404 page and halts script execution
     *
     * @param string $sPage     The URI which 404'd
     * @param bool   $bLogError Whether to log the error
     *
     * @return void
     */
    public function show_404($sPage = '', $bLogError = true)
    {
        $oErrorHandler = Factory::service('ErrorHandler');;
        $oErrorHandler->show404Screen($sPage, $bLogError);
    }
}
