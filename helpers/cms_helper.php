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
     * @param  string $idSlug The block's ID or slug
     * @return mixed          String on success, false on failure
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
     * @param  string $idSlug The slider's ID or slug
     * @return mixed          stdClass on success, false on failure
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

// --------------------------------------------------------------------------

if (!function_exists('cmsMenu'))
{
    /**
     * Returns a CMS menu
     * @param  string $idSlug The menu's ID or slug
     * @return mixed          stdClass on success, false on failure
     */
    function cmsMenu($idSlug)
    {
        get_instance()->load->model('cms/cms_menu_model');
        $menu = get_instance()->cms_menu_model->get_by_id_or_slug($idSlug);

        if (!$menu) {

            return false;
        }

        return $menu;
    }
}
