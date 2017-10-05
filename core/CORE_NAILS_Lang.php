<?php

/**
 * This class overrides some default CodeIgniter language handling, namely allowing
 * parameters to be provided to line() method
 *
 * @package     Nails
 * @subpackage  common
 * @category    language
 * @author      Nails Dev Team
 * @link
 */

if (!class_exists('MX_Lang')) {

    require NAILS_COMMON_PATH . 'MX/Lang.php';
}

class CORE_NAILS_Lang extends MX_Lang
{
    /**
     * Overriding the default line() method so that parameters can be specified
     * @param   string  $line   the language line
     * @param   array   $params any parameters to sub in
     * @return  string
     */
    public function line($line = '', $params = null)
    {
        if (empty($params)) {
            return parent::line($line);
        }

        //  We have some parameters, sub 'em in or the unicorns will die.
        $line = parent::line($line);

        if ($line !== false) {

            if (is_array($params)) {

                $line = vsprintf($line, $params);

            } else {

                $line = sprintf($line, $params);
            }
        }

        return $line;
    }

    // --------------------------------------------------------------------------

    /**
     * Loads a lang file
     * @param  string  $langfile   The lang file to load
     * @param  string  $lang       The language to load
     * @param  boolean $return     Whether to return the file or not
     * @param  boolean $add_suffix The suffix to add
     * @param  string  $alt_path   An alternative path
     * @param  string  $_module    The module to look in
     * @return mixed
     */
    public function load($langfile, $lang = '', $return = false, $add_suffix = true, $alt_path = '', $_module = '')
    {
        // Proxy check to determine whether runtime was initiated through CodeIgniter
        if (!class_exists('CI')) {

            /* Check failed; abandon this attempt to utilise CodeIgniter templating/localisation constructs
                => a null/void return type is consistent with behaviour in the CI_Lang base class
            */
            return;
        }

        //  Are we loading an array of languages? If so, handle each one on its own.
        if (is_array($langfile)) {

            foreach ($langfile as $_lang) {

                $this->load($_lang);
            }

            return $this->language;
        }

        // --------------------------------------------------------------------------

        //  Determine which language we're using, if not specified, use the app's default
        $_default = CI::$APP->config->item('language');
        $idiom    = $lang == '' ? $_default : $lang;

        // --------------------------------------------------------------------------

        //  Check to see if the language file has already been loaded
        if (in_array($langfile . '_lang' . EXT, $this->is_loaded, true)) {

            return $this->language;
        }

        // --------------------------------------------------------------------------

        //  Look for the language
        $_module || $_module = CI::$APP->router->fetch_module();
        list($path, $_langfile) = Modules::find($langfile . '_lang', $_module, 'language/' . $idiom . '/');

        /**
         * Confession. I'm not entirely sure how/why this works. Dumping out debug statements confuses
         * me as they don't make sense, but the right lang files seem to be loaded. Sorry, future Pablo.
         **/

        if ($path === false) {

            //  File not found, fallback to the default language if not already using it
            if ($idiom != $_default) {

                //  Using MXs version seems to work as expected.
                if ($lang = parent::load($langfile, $_default, $return, $add_suffix, $alt_path)) {

                    return $lang;
                }

            } else {

                //  Not found within modules, try normal load()
                if ($lang = CI_Lang::load($langfile, $idiom, $return, $add_suffix, $alt_path)) {

                    return $lang;
                }
            }

        } else {

            //  Lang file was found. Load it.
            if ($lang = Modules::load_file($_langfile, $path, 'lang')) {

                if ($return) {

                    return $lang;
                }

                $this->language = array_merge($this->language, $lang);
                $this->is_loaded[] = $langfile.'_lang'.EXT;
                unset($lang);
            }
        }

        // --------------------------------------------------------------------------

        return $this->language;
    }
}
