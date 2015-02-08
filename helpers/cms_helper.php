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
