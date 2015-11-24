<?php

/**
 * Alters CI Pagination functionality
 *
 * @package     Nails
 * @subpackage  common
 * @category    Library
 * @author      Nails Dev Team
 * @link
 */

class CORE_NAILS_User_agent extends CI_User_agent
{
    /**
     * Compiles the user agent from a supplied string
     * @param  string $str The string to compile
     * @return stdClass
     */
    public function from_string($str = '')
    {
        //  Get browser info
        $browser = $this->_get_browser($str);

        //  Set output
        $out           = new stdClass();
        $out->platform = $this->_get_platform($str);
        $out->browser  = $browser['browser'];
        $out->version  = $browser['version'];

        return $out;
    }

    // --------------------------------------------------------------------------

    /**
     * Gets the browser (modified copy of parent method: _set_browser)
     * @param  string $agent The string to compile
     * @return array
     */
    private function _get_browser($agent)
    {
        $out = array('version' => 'Unknown Version', 'browser' => 'Unknown Browser');
        if (is_array($this->browsers) && count($this->browsers) > 0) {
            foreach ($this->browsers as $key => $val) {
                if (preg_match("|".preg_quote($key).".*?([0-9\.]+)|i", $agent, $match)) {
                    $out['version'] = $match[1];
                    $out['browser'] = $val;
                    return $out;
                }
            }
        }
        return $out;
    }

    // --------------------------------------------------------------------------

    /**
     * Gets the platform (modified copy of parent method: _set_platform)
     * @param   string $agent The string to compile
     * @return  string
     */
    private function _get_platform($agent)
    {
        if (is_array($this->platforms) && count($this->platforms) > 0) {
            foreach ($this->platforms as $key => $val) {
                if (preg_match("|".preg_quote($key)."|i", $agent)) {
                    return $val;
                }
            }
        }
        return 'Unknown Platform';
    }
}
