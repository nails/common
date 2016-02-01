<?php

/**
 * This class brings about uniformity to Nails driver models.
 *
 * @package     Nails
 * @subpackage  common
 * @category    model
 * @author      Nails Dev Team
 * @link
 */

namespace Nails\Common\Model;

use Nails\Common\Model\BaseComponent;

class BaseDriver extends BaseComponent
{
    /**
     * The type of component to load up
     * @var string
     */
    protected $sComponentType = 'driver';

    // --------------------------------------------------------------------------

    /**
     * Which app setting (in the $sModule grouping) defines the array of enabled
     * components slugs.
     * @var string
     */
    protected $sEnabledSetting = 'enabled_drivers';

    // --------------------------------------------------------------------------

    /**
     * The array of driver instances, created on demand.
     * @var array
     */
    protected $Instances = array();

    // --------------------------------------------------------------------------

    /**
     * Return an instance of the driver.
     * @param  string $sSlug The driver's slug
     * @return mixed
     */
    public function getInstance($sSlug)
    {
        if (isset($this->aInstances[$sSlug])) {

            return $this->aInstances[$sSlug];

        } else {

            foreach ($this->aEnabled as $oDriver) {

                if ($sSlug == $oDriver->slug) {

                    $this->aInstances[$sSlug] = _NAILS_GET_DRIVER_INSTANCE($oDriver);

                    //  Apply driver configurations
                    $aSettings = array(
                        'sSlug' => $oDriver->slug
                    );
                    if (!empty($oDriver->data->settings)) {
                        $aSettings = array_merge(
                            $aSettings,
                            $this->extractComponentSettings(
                                $oDriver->data->settings,
                                $oDriver->slug
                            )
                        );
                    }

                   $this->aInstances[$sSlug]->setConfig($aSettings);

                    return $this->aInstances[$sSlug];
                }
            }
        }

        return null;
    }
}
