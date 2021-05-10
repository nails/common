<?php

/**
 * This class brings about uniformity to Nails driver models.
 *
 * @package                   Nails
 * @subpackage                common
 * @category                  model
 * @author                    Nails Dev Team
 * @link
 * @todo (Pablo - 2019-03-22) - Drivers shouldn't be in the "model" namespace
 */

namespace Nails\Common\Model;

use Nails\Common\Exception\NailsException;
use Nails\Common\Factory\Component;
use Nails\Components;

abstract class BaseDriver extends BaseComponent
{
    /**
     * The type of component to load up
     *
     * @var string
     */
    protected $sComponentType = 'driver';

    // --------------------------------------------------------------------------

    /**
     * The interface which the drivers must implement
     *
     * @var string|null
     */
    protected $sMustImplement = null;

    // --------------------------------------------------------------------------

    /**
     * The array of driver instances, created on demand.
     *
     * @var array
     */
    protected $aInstances = [];

    // --------------------------------------------------------------------------

    /**
     * Return an instance of the driver.
     *
     * @param Component|string $sSlug The driver's slug
     *
     * @return mixed
     */
    public function getInstance($sSlug)
    {
        if ($sSlug instanceof Component) {
            $sSlug = $sSlug->slug;
        }

        if (isset($this->aInstances[$sSlug])) {
            return $this->aInstances[$sSlug];

        } else {

            foreach ($this->aComponents as $oDriverConfig) {
                if ($sSlug === $oDriverConfig->slug) {
                    $oDriver = $oDriverConfig;
                    break;
                }
            }

            if (!empty($oDriver)) {

                $oInstance = Components::getDriverInstance($oDriver);

                if ($this->sMustImplement && !classImplements($oInstance, $this->sMustImplement)) {
                    throw new NailsException(sprintf(
                        'Driver "%s" must implement "%s"',
                        get_class($oInstance),
                        $this->sMustImplement
                    ));
                }

                //  Apply driver configurations
                $aSettings = array_merge(
                    ['sSlug' => $sSlug],
                    appSetting(null, $sSlug)
                );

                $this->aInstances[$sSlug] = $oInstance;
                $this->aInstances[$sSlug]->setConfig($aSettings);

                return $this->aInstances[$sSlug];
            }
        }

        return null;
    }
}
