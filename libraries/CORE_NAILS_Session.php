<?php

/**
 * Alters CI Session functionality
 *
 * @package     Nails
 * @subpackage  common
 * @category    Library
 * @author      Nails Dev Team
 * @link
 */

class CORE_NAILS_Session extends CI_Session
{
    /**
     * Keeps existing flashdata available to next request.
     * http://codeigniter.com/forums/viewthread/104392/#917834
     * @param  $key The key to keep, null will retain all flashdata
     * @return void
     **/
    public function keep_flashdata($key = null)
    {
        /**
         * 'old' flashdata gets removed.  Here we mark all flashdata as 'new' to preserve
         * it from _flashdata_sweep(). Note the function will NOT return FALSE if the $key
         * provided cannot be found, it will retain ALL flashdata
         */

        if (is_null($key)) {

            foreach ($this->userdata as $k => $v) {

                $old_flashdata_key = $this->flashdata_key . ':old:';

                if (strpos($k, $old_flashdata_key) !== false) {

                    $new_flashdata_key = $this->flashdata_key . ':new:';
                    $new_flashdata_key = str_replace($old_flashdata_key, $new_flashdata_key, $k);
                    $this->set_userdata($new_flashdata_key, $v);
                }
            }

            return true;

        } elseif (is_array($key)) {

            foreach ($key as $k) {

                $this->keep_flashdata($k);
            }
        }

        // --------------------------------------------------------------------------

        $old_flashdata_key = $this->flashdata_key.':old:' . $key;
        $value = $this->userdata($old_flashdata_key);

        // --------------------------------------------------------------------------

        $new_flashdata_key = $this->flashdata_key.':new:' . $key;
        $this->set_userdata($new_flashdata_key, $value);
    }
}
