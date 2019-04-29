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

namespace Nails\Common\CodeIgniter\Core;

use CI_Exceptions;
use Nails\Common\Exception\NailsException;
use Nails\Factory;

class Exceptions extends CI_Exceptions
{
    /**
     * Override the show_error method and pass to the Nails ErrorHandler
     *
     * @param string  $sSubject      The error's subject
     * @param string  $sMessage      The error message
     * @param string  $sTemplate     Unused; only there to suppress compatibility notification
     * @param int     $iStatusCode   Unused; only there to suppress compatibility notification
     * @param boolean $bUseException Whether to use an exception
     *
     * @throws NailsException
     * @return void
     */
    public function show_error(
        $sSubject,
        $sMessage = '',
        $sTemplate = '500',
        $iStatusCode = 500,
        $bUseException = true
    ) {
        if (is_array($sMessage)) {
            $sMessage = implode('<br>', $sMessage);
        }

        if ($bUseException) {
            throw new NailsException($sMessage, $iStatusCode);
        } else {
            $oErrorHandler = Factory::service('ErrorHandler');
            $oErrorHandler->showFatalErrorScreen($sSubject, $sMessage);
        }
    }

    // --------------------------------------------------------------------------

    /**
     * Override the show_exception method and pass to the Nails ErrorHandler
     *
     * @param \Exception $oException
     */
    public function show_exception($oException)
    {
        $oErrorHandler = Factory::service('ErrorHandler');
        $sMessage      = implode(
            '; ',
            [
                'Code: ' . $oException->getCode(),
                'File: ' . $oException->getFile(),
                'Line: ' . $oException->getLine(),
            ]
        );
        $oErrorHandler->showFatalErrorScreen($oException->getMessage(), $sMessage);
    }

    // --------------------------------------------------------------------------

    /**
     * Overrides the show_php_error method in order to track errors
     *
     * @param int    $iSeverity
     * @param string $sMessage
     * @param string $sFilePath
     * @param int    $iLine
     *
     * @return string
     */
    public function show_php_error($iSeverity, $sMessage, $sFilePath, $iLine)
    {
        $oErrorHandler = Factory::service('ErrorHandler');
        return $oErrorHandler->triggerError($iSeverity, $sMessage, $sFilePath, $iLine);
    }

    // --------------------------------------------------------------------------

    /**
     * Renders the 404 page and halts script execution
     *
     * @param bool $bLogError Whether to log the error
     *
     * @return void
     */
    public function show_404($sPage = '', $bLogError = true)
    {
        $oErrorHandler = Factory::service('ErrorHandler');
        $oErrorHandler->show404($bLogError);
    }

    // --------------------------------------------------------------------------

    /**
     * Renders the 401 page and halts script execution
     *
     * @param bool $bLogError Whether to log the error
     *
     * @return void
     */
    public function show_401($bLogError = true)
    {
        $oErrorHandler = Factory::service('ErrorHandler');
        $oErrorHandler->show401($bLogError);
    }
}
