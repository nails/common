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

namespace Nails\Common\Library;

class View
{
    /**
     * Reference to the CI super object
     * @var \CI_Controller
     */
    protected $oCi;

    // --------------------------------------------------------------------------

    /**
     * View constructor.
     */
    public function __construct()
    {
        $this->oCi = get_instance();
    }

    // --------------------------------------------------------------------------

    /**
     * Loads a view
     * @param string $sView   The view to load
     * @param array  $aData   Data to pass to the view
     * @param bool   $bReturn Whether to return the view or not
     * @return mixed
     */
    public function load($sView, $aData = array(), $bReturn = false)
    {
        if (!$bReturn) {
            $this->oCi->load->view($sView, $aData, $bReturn);
            return $this;
        } else {
            return $this->oCi->load->view($sView, $aData, $bReturn);
        }
    }
}
