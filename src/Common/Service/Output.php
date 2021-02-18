<?php

/**
 * The class abstracts CodeIgniter's Output class.
 *
 * @package     Nails
 * @subpackage  common
 * @category    Library
 * @author      Nails Dev Team
 * @link
 * @todo        Remove dependency on CI
 */

namespace Nails\Common\Service;

use Nails\Common\Events;
use Nails\Common\Exception\FactoryException;
use Nails\Factory;

/**
 * Class Output
 *
 * @package Nails\Common\Service
 */
class Output
{
    /**
     * The CodeIgniter Output object
     *
     * @var \CI_Output
     */
    protected $oOutput;

    // --------------------------------------------------------------------------

    /**
     * Output constructor.
     */
    public function __construct()
    {
        $oCi           = get_instance();
        $this->oOutput = $oCi->output;

        // --------------------------------------------------------------------------
        /**
         * Overload the display function
         */
        $oCi->hooks->addHook(
            'display_override',
            [
                'classref' => $this,
                'method'   => 'display',
                'params'   => [],
            ]
        );
    }

    // --------------------------------------------------------------------------

    /**
     * Renders
     *
     * @throws FactoryException
     */
    public function display()
    {
        /** @var Event $oEventService */
        $oEventService = Factory::service('Event');
        $oEventService->trigger(Events::OUTPUT_PRE);
        $this->oOutput->_display();
        $oEventService->trigger(Events::OUTPUT_POST);
    }

    // --------------------------------------------------------------------------

    /**
     * Returns the current output string
     *
     * @return string|null
     */
    public function getOutput(): ?string
    {
        return $this->oOutput->get_output();
    }

    // --------------------------------------------------------------------------

    /**
     * Sets the output string
     *
     * @param string $sOutput The output to set
     *
     * @return $this
     */
    public function setOutput(string $sOutput): self
    {
        $this->oOutput->set_output($sOutput);
        return $this;
    }

    // --------------------------------------------------------------------------

    /**
     * Append to the existing output string
     *
     * @param string $sOutput The output to append
     *
     * @return $this
     */
    public function appendOutput(string $sOutput): self
    {
        $this->oOutput->append_output($sOutput);
        return $this;
    }

    // --------------------------------------------------------------------------

    /**
     * Set a header to be sent with final output
     *
     * @param string $sHeader  the header to set
     * @param bool   $bReplace Whether to replace the old header value, if set
     *
     * @return $this
     */
    public function setHeader(string $sHeader, bool $bReplace = true): self
    {
        $this->oOutput->set_header($sHeader, $bReplace);
        return $this;
    }

    // --------------------------------------------------------------------------

    /**
     * Set the Content-Type header
     *
     * @param string      $sMimeType The mime type of the output
     * @param string|null $sCharset  The charset of the output
     *
     * @return $this
     */
    public function setContentType(string $sMimeType, string $sCharset = null): self
    {
        $this->oOutput->set_content_type($sMimeType, $sCharset);
        return $this;
    }

    // --------------------------------------------------------------------------

    /**
     * Get the current Content-Type header
     *
     * @return string
     */
    public function getContentType(): string
    {
        return $this->oOutput->get_content_type();
    }

    // --------------------------------------------------------------------------

    /**
     * Get a header
     *
     * @param string $sHeader The header to get
     *
     * @return string|null
     */
    public function getHeader(string $sHeader): ?string
    {
        return $this->oOutput->get_header($sHeader);
    }

    // --------------------------------------------------------------------------

    /**
     * Sets the HTTP status header
     *
     * @param int    $iCode The status code
     * @param string $sText The status text
     *
     * @return $this
     */
    public function setStatusHeader($iCode = 200, $sText = ''): self
    {
        $this->oOutput->set_status_header($iCode, $sText);
        return $this;
    }

    // --------------------------------------------------------------------------

    /**
     * Enable the CI profiler
     *
     * @return $this
     */
    public function enableProfiler(): self
    {
        $this->oOutput->enable_profiler(true);
        return $this;
    }

    // --------------------------------------------------------------------------

    /**
     * Disable the CI profiler
     *
     * @return $this
     */
    public function disableProfiler(): self
    {
        $this->oOutput->enable_profiler(false);
        return $this;
    }

    // --------------------------------------------------------------------------

    /**
     * Set profiler sections
     *
     * @param array $aSections Profiler sections
     *
     * @return $this
     */
    public function setProfilerSections(array $aSections): self
    {
        $this->oOutput->set_profiler_sections($aSections);
        return $this;
    }

    // --------------------------------------------------------------------------

    /**
     * Set Cache Header
     *
     * Set the HTTP headers to match the server-side file cache settings
     * in order to reduce bandwidth.
     *
     * @param int $iLastModified Timestamp of when the page was last modified
     * @param int $iExpiration   Timestamp of when should the requested page expire from cache
     *
     * @return $this
     */
    public function setCacheHeader(int $iLastModified, int $iExpiration): self
    {
        $this->oOutput->set_cache_header($iLastModified, $iExpiration);
        return $this;
    }

    // --------------------------------------------------------------------------
    //  The following methods exist only to to ensure backwards compatability
    //  with CodeIgniter and should be considered deprecated
    // --------------------------------------------------------------------------

    /**
     * Route calls to the CodeIgniter Output class
     *
     * @param string $sMethod    The method being called
     * @param array  $aArguments Any arguments being passed
     *
     * @return mixed
     */
    public function __call($sMethod, $aArguments)
    {
        if (method_exists($this, $sMethod)) {
            return call_user_func_array([$this, $sMethod], $aArguments);
        } else {
            return call_user_func_array([$this->oOutput, $sMethod], $aArguments);
        }
    }

    // --------------------------------------------------------------------------

    /**
     * Pass any property "gets" to the CodeIgniter Output class
     *
     * @param string $sProperty The property to get
     *
     * @return mixed
     */
    public function __get($sProperty)
    {
        return $this->oOutput->{$sProperty};
    }

    // --------------------------------------------------------------------------

    /**
     * Pass any property "sets" to the CodeIgniter Output class
     *
     * @param string $sProperty The property to set
     * @param mixed  $mValue    The value to set
     *
     * @return void
     */
    public function __set($sProperty, $mValue)
    {
        $this->oOutput->{$sProperty} = $mValue;
    }
}
