<?php

/**
 * This class brings about uniformity to Nails skins
 *
 * @package     Nails
 * @subpackage  common
 * @category    model
 * @author      Nails Dev Team
 * @link
 */

namespace Nails\Common\Model;

use Nails\Common\Model\BaseComponent;

class BaseSkin extends BaseComponent
{
    /**
     * The type of component to load up
     * @var string
     */
    protected $sComponentType = 'skin';

    // --------------------------------------------------------------------------

    /**
     * Which app setting (in the $sModule grouping) defines the array of enabled
     * components slugs.
     * @var string
     */
    protected $sEnabledSetting = 'enabled_skins';
}
