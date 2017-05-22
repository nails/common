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

abstract class BaseDriver extends BaseComponent
{
    /**
     * The type of component to load up
     * @var string
     */
    protected $sComponentType = 'driver';

    // --------------------------------------------------------------------------

    /**
     * The array of driver instances, created on demand.
     * @var array
     */
    protected $aInstances = [];

    // --------------------------------------------------------------------------

    /**
     * Return an instance of the driver.
     *
     * @param  string $sSlug The driver's slug
     *
     * @return mixed
     */
    public function getInstance($sSlug)
    {
        if (isset($this->aInstances[$sSlug])) {

            return $this->aInstances[$sSlug];

        } else {

            foreach ($this->aComponents as $oDriverConfig) {
                if ($sSlug == $oDriverConfig->slug) {
                    $oDriver = $oDriverConfig;
                    break;
                }
            }

            if (!empty($oDriver)) {

                $this->aInstances[$oDriver->slug] = _NAILS_GET_DRIVER_INSTANCE($oDriver);

                //  Apply driver configurations
                $aSettings = [
                    'sSlug' => $oDriver->slug,
                ];
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

        return null;
    }
}
