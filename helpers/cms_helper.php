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

if (!function_exists('cmsBlock')) {

    /**
     * Returns a block's value
     * @param  string $idSlug The block's ID or slug
     * @return string
     */
    function cmsBlock($slug)
    {
        get_instance()->load->model('cms/cms_block_model');
        $block = get_instance()->cms_block_model->get_by_slug($slug);

        if (!$block) {

            return '';
        }

        return $block->value;
    }
}

// --------------------------------------------------------------------------

if (!function_exists('cmsSlider')) {

    /**
     * Returns a CMS slider
     * @param  string $idSlug The slider's ID or slug
     * @return mixed          stdClass on success, false on failure
     */
    function cmsSlider($idSlug)
    {
        get_instance()->load->model('cms/cms_slider_model');
        return get_instance()->cms_slider_model->get_by_id_or_slug($idSlug);
    }
}

// --------------------------------------------------------------------------

if (!function_exists('cmsMenu')) {

    /**
     * Returns a CMS menu
     * @param  string $idSlug The menu's ID or slug
     * @return mixed          stdClass on success, false on failure
     */
    function cmsMenu($idSlug)
    {
        get_instance()->load->model('cms/cms_menu_model');
        return get_instance()->cms_menu_model->get_by_id_or_slug($idSlug);
    }
}

// --------------------------------------------------------------------------

if (!function_exists('cmsMenuNested')) {

    /**
     * Returns a CMS menu
     * @param  string $idSlug The menu's ID or slug
     * @return mixed          stdClass on success, false on failure
     */
    function cmsMenuNested($idSlug)
    {
        get_instance()->load->model('cms/cms_menu_model');
        $data = array('nestItems' => true);
        return get_instance()->cms_menu_model->get_by_id_or_slug($idSlug, $data);
    }
}

// --------------------------------------------------------------------------

if (!function_exists('cmsPage')) {

    /**
     * Returns a CMS page
     * @param  string $idSlug The page's ID or slug
     * @return mixed          stdClass on success, false on failure
     */
    function cmsPage($idSlug)
    {
        get_instance()->load->model('cms/cms_page_model');
        return get_instance()->cms_page_model->get_by_id_or_slug($idSlug);
    }
}
