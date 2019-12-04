<?php

/**
 * The class abstracts CI's Zip class.
 *
 * @package     Nails
 * @subpackage  common
 * @category    Library
 * @author      Nails Dev Team
 * @link
 */

namespace Nails\Common\Service;

use Nails\Common\Exception\NailsException;

/**
 * Class Zip
 *
 * @package Nails\Common\Service
 *
 * @property $zipdata           = '';
 * @property $directory         = '';
 * @property $entries           = 0;
 * @property $file_num          = 0;
 * @property $offset            = 0;
 * @property $now               = null;
 * @property $compression_level = 2;
 *
 * @method add_dir($directory)
 * @method add_data($filepath, $data = null)
 * @method read_file($path, $archive_filepath = false)
 * @method read_dir($path, $preserve_filepath = true, $root_path = null)
 * @method get_zip()
 * @method archive($filepath)
 * @method clear_data()
 */
class Zip
{
    /**
     * The zip object
     *
     * @var \CI_Zip
     */
    private $oZip;

    // --------------------------------------------------------------------------

    /**
     * Zip constructor.
     */
    public function __construct()
    {
        require_once NAILS_CI_SYSTEM_PATH . 'libraries/Zip.php';
        $this->oZip = new \CI_Zip();
    }

    // --------------------------------------------------------------------------

    /**
     * Route calls to the CodeIgniter Zip class
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
            return call_user_func_array([$this->oZip, $sMethod], $aArguments);
        }
    }

    // --------------------------------------------------------------------------

    /**
     * Pass any property "gets" to the CodeIgniter Zip class
     *
     * @param string $sProperty The property to get
     *
     * @return mixed
     */
    public function __get($sProperty)
    {
        return $this->oZip->{$sProperty};
    }

    // --------------------------------------------------------------------------

    /**
     * Pass any property "sets" to the CodeIgniter Zip class
     *
     * @param string $sProperty The property to set
     * @param mixed  $mValue    The value to set
     *
     * @return void
     */
    public function __set($sProperty, $mValue)
    {
        $this->oZip->{$sProperty} = $mValue;
    }

    // --------------------------------------------------------------------------

    /**
     * @param string $filename
     *
     * @throws NailsException
     */
    public function download($filename = 'backup.zip')
    {
        if (!function_exists('get_instance')) {
            throw new NailsException('Cannot download; CodeIgniter not available');
        }

        parent::download($filename);
    }
}
