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
use Nails\Common\Exception\NailsException;

abstract class BaseSkin extends BaseComponent
{
    /**
     * The type of component to load up
     * @var string
     */
    protected $sComponentType = 'skin';

    // --------------------------------------------------------------------------

    /**
     * Contains the variables passed to the view for easy nesting
     * @var array
     */
    protected $aCachedViewData = array();

    // --------------------------------------------------------------------------

    /**
     * Load a view contained within a skin
     * @param  string  $sSlug   The skin's slug
     * @param  string  $sView   The view to load
     * @param  array   $aData   The data to pass to the view
     * @param  boolean $bReturn whether to return the string, or send to the browser
     * @return mixed
     */
    public function view($sSlug, $sView, $aData = array(), $bReturn = false)
    {
        //  Check validity of slug
        $aSkins = $this->getAll();
        $oSkin  = null;

        foreach ($aSkins as $oSkinConfig) {
            if ($oSkinConfig->slug == $sSlug) {
                $oSkin = $oSkinConfig;
                break;
            }
        }

        if (empty($oSkin)) {
            throw new NailsException('"' . $sSlug . '" skin does not exist.', 1);
        }

        //  Check for existance of view
        $sViewPath = $oSkin->path . 'views/' . $sView . '.php';

        if (!file_exists($sViewPath)) {
            throw new NailsException('Could not load view at "' . $sViewPath . '"', 1);
        }

        //  Load the view; highly inspired by Codeigniter

        /*
         * Extract and cache variables
         * Variables are cached so that nested views can access them properly.
         */

        $this->aCachedViewData['current_skin'] = $sSlug;

        if (is_array($aData)) {
            $this->aCachedViewData = array_merge($this->aCachedViewData, $aData);
        }
        extract($this->aCachedViewData);


        /*
         * #cirip #sorrynotsorry
         *
         * Buffer the output
         *
         * We buffer the output for two reasons:
         * 1. Speed. You get a significant speed boost.
         * 2. So that the final rendered template can be
         * post-processed by the output class.  Why do we
         * need post processing?  For one thing, in order to
         * show the elapsed page load time.  Unless we
         * can intercept the content right before it's sent to
         * the browser and then stop the timer it won't be accurate.
         */
        ob_start();

        //  Load the view
        include $sViewPath;

        $sBuffer = ob_get_contents();
        @ob_end_clean();

        // Return the file data if requested
        if ($bReturn === true) {
            return $sBuffer;
        }

        echo $sBuffer;
        return $this;
    }
}
