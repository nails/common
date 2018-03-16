<?php

/**
 * The class provides a convenient way to load views
 *
 * @package     Nails
 * @subpackage  common
 * @category    Library
 * @author      Nails Dev Team
 * @link
 */

namespace Nails\Common\Service;

use Nails\Common\Exception\ViewNotFoundException;
use Nails\Common\Traits\Caching;
use Nails\Factory;

class View
{
    use Caching;

    // --------------------------------------------------------------------------

    /**
     * An array of data which is passed to the views
     * @var array
     */
    protected $aData = [];

    // --------------------------------------------------------------------------

    /**
     * The paths to look for views in
     * @var array
     */
    protected $aViewPaths = [];

    /**
     * Stores the current buffer level
     * @var int
     */
    protected $iBufferLevel;

    // --------------------------------------------------------------------------

    /**
     * View constructor.
     */
    public function __construct()
    {
        $this->aData        = &getControllerData();
        $this->iBufferLevel = ob_get_level();
        $this->aViewPaths   = [
            APPPATH . 'modules/',
            APPPATH,
            NAILS_COMMON_PATH,
        ];
        foreach (_NAILS_GET_MODULES() as $oModule) {
            $this->aViewPaths[] = $oModule->path;
        }

        $oRouter = Factory::service('Router');
    }

    // --------------------------------------------------------------------------

    /**
     * Get an item from the view data array
     *
     * @param string $sKey The key to retrieve
     *
     * @return array|mixed|null
     */
    public function getData($sKey = null)
    {
        if (is_null($sKey)) {
            return $this->aData;
        } elseif (array_key_exists($sKey, $this->aData)) {
            return $this->aData[$sKey];
        } else {
            return null;
        }
    }

    // --------------------------------------------------------------------------

    /**
     * Add an item to the view data array, or update an existing item
     *
     * @param string|array $mKey   The key, or keys (in a key value pair), to set
     * @param mixed        $mValue The value to set
     *
     * @throws \Exception
     * @returns $this
     */
    public function setData($mKey, $mValue = null)
    {
        if (is_array($mKey)) {
            foreach ($mKey as $sKey => $mSubValue) {
                $this->setData($sKey, $mSubValue);
            }
        } elseif (is_string($mKey) || is_numeric($mKey)) {
            $this->aData[$mKey] = $mValue;
        } else {
            throw new \Exception('Key must be a string or a numeric');
        }

        return $this;
    }

    // --------------------------------------------------------------------------

    /**
     * Unset an item from the view data array
     *
     * @param string|array $mKey The key, or keys, to unset
     *
     * @return $this
     */
    public function unsetData($mKey)
    {
        if (is_array($mKey)) {
            foreach ($mKey as $sSubKey) {
                $this->unsetData($sSubKey);
            }
        } else {
            unset($this->aData[$mKey]);
        }

        return $this;
    }

    // --------------------------------------------------------------------------

    /**
     * Loads a view
     *
     * @param string|array $mView   The view to load, or an array of views to load
     * @param array        $aData   Data to pass to the view(s)
     * @param boolean      $bReturn Whether to return the view(s) or not
     *
     * @return mixed
     */
    public function load($mView, $aData = [], $bReturn = false)
    {
        if (is_array($mView)) {

            $sOut = '';
            foreach ($mView as $sView) {
                if ($bReturn) {
                    $sOut .= $this->load($sView, $aData, $bReturn);
                } else {
                    $this->load($sView, $aData, $bReturn);
                }
            }
            return $bReturn ? $sOut : $this;

        } elseif (is_string($mView)) {

            $aData = array_merge($this->getData(), (array) $aData);
            ob_start();

            $sResolvedPath = $this->resolvePath($mView);
            if (!$sResolvedPath) {
                @ob_end_clean();
                throw new ViewNotFoundException('Could not resolve view "' . $mView . '"');
            }

            extract($aData);
            include $sResolvedPath;

            if ($bReturn) {
                $sBuffer = ob_get_contents();
                @ob_end_clean();
                return $sBuffer;
            } elseif (ob_get_level() > $this->iBufferLevel + 1) {
                ob_end_flush();
            } else {
                $oOutput = Factory::service('Output');
                $oOutput->append_output(ob_get_contents());
                @ob_end_clean();
            }

            return $this;
        } else {
            return $this;
        }
    }

    // --------------------------------------------------------------------------

    /**
     * Attempts to resolve the view path
     *
     * @param stirng $sView The view to resolve
     *
     * @return bool|string
     */
    protected function resolvePath($sView)
    {
        $sCacheKey = 'path:' . $sView;
        $sCached   = $this->getCache($sCacheKey);
        if ($sCached !== null) {
            return $sCached;
        }

        $sView         = preg_replace('/\.php$/', '', $sView) . '.php';
        $sResolvedPath = '';
        $oRouter       = Factory::service('Router');

        if (strpos($sView, '/') !== 0) {
            foreach ($this->aViewPaths as $sPath) {

                $aPath = explode('/', $sView);
                if (count($aPath) > 1) {
                    $sModule = array_shift($aPath) . '/';
                    $sFile   = implode('/', $aPath);
                } else {
                    $sModule = '';
                    $sFile   = $sView;
                }

                $aPathOptions = [
                    $sPath . $sModule . 'views/' . $sFile,
                    $sPath . $oRouter->current_module() . '/views/' . $sModule . $sFile,
                    $sPath . 'views/' . $sModule . $sFile,
                ];

                foreach ($aPathOptions as $sCompiledPath) {
                    if (file_exists($sCompiledPath)) {
                        $sResolvedPath = $sCompiledPath;
                        break 2;
                    }
                }
            }
        } else {
            $sResolvedPath = file_exists($sView) ? $sView : false;
        }

        $this->setCache($sCacheKey, $sResolvedPath);

        return $sResolvedPath;
    }

    // --------------------------------------------------------------------------

    /**
     * Backwards compatability with CodeIgniter; allows views to load items using $this
     *
     * @param string $sProperty The property being requested
     *
     * @return null|mixed
     */
    public function __get($sProperty)
    {
        return function_exists('get_instance') ? get_instance()->{$sProperty} : null;
    }
}
