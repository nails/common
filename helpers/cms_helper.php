<?php

/**
 * This helper brings some convinient functions for interacting with CMS elements
 *
 * @package     Nails
 * @subpackage  module-cms
 * @category    Helper
 * @author      Nails Dev Team
 * @link
 */

if (!function_exists('cmsBlock'))
{
    /**
     * Returns a block's value
     * @param  string $slug The block's slug
     * @return mixed        String on success, false on failure
     */
    function cmsBlock($slug)
    {
        get_instance()->load->model('cms/cms_block_model');
        $block = get_instance()->cms_block_model->get_by_slug($slug);

        if (!$block) {

            return false;
        }

        return $block->value;
    }
}

// --------------------------------------------------------------------------

if (!function_exists('cmsSlider'))
{
    /**
     * Returns a CMS slider
     * @param  string $slug The slider's ID or slug
     * @return mixed        stdClass on success, false on failure
     */
    function cmsSlider($idSlug)
    {
        get_instance()->load->model('cms/cms_slider_model');
        $slider = get_instance()->cms_block_model->get_by_id_or_slug($idSlug);

        if (!$slider) {

            return false;
        }

        return $slider;
    }
}
