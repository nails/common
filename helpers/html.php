<?php

/**
 * This file provides HTML related helper functions
 *
 * @package     Nails
 * @subpackage  common
 * @category    Helper
 * @author      Nails Dev Team
 * @link
 */

/**
 * Image
 *
 * Generates an <img /> element, modded to always include the alt attribute
 * if it's not already defined
 *
 * @access  public
 * @param   mixed
 * @return  string
 */
if (!function_exists('img')) {
    function img($src, $alt = false, $title = false, $index_page = true)
    {
        if (!is_array($src)) {
            $src = array('src' => $src);
        }

        // Modded by Pablo here...
        // Ensure that the 'alt' and 'title' attributes are always in the markup (for validation)
        if (!array_key_exists('alt', $src)) {
            $src['alt'] = $alt;
        }

        $title = ($title === false) ? $alt : $title;
        if (!array_key_exists('title', $src)) {
            $src['title'] = $title;
        }
        // ... to here.

        $img = '<img';

        foreach ($src as $k => $v) {

            if ($k == 'src' && strpos($v, '://') === false) {
                $CI =& get_instance();

                if ($index_page === true) {
                    $img .= ' src="'.$CI->config->site_url($v).'" ';
                } else {
                    $img .= ' src="'.$CI->config->slash_item('base_url').$v.'" ';
                }
            } else {
                $img .= " $k=\"$v\" ";
            }
        }

        $img .= '/>';

        //  Force SSL for local images if running on non-standard port
        if (isset($_SERVER['HTTPS']) && strtolower($_SERVER['HTTPS']) == 'on') :

            $site_url_ssl = str_replace('http://', 'https://', site_url());
            $img = str_replace(site_url(), $site_url_ssl, $img);

        endif;

        return $img;
    }
}


/**
 * Link
 *
 * Generates link to a CSS file
 *
 * @access  public
 * @param   mixed   stylesheet hrefs or an array
 * @param   string  rel
 * @param   string  type
 * @param   string  title
 * @param   string  media
 * @param   boolean should index_page be added to the css path
 * @return  string
 */
if (!function_exists('link_tag')) {

    function link_tag($href = '', $rel = 'stylesheet', $type = 'text/css', $title = '', $media = '', $index_page = true)
    {
        $CI =& get_instance();

        $link = '<link ';

        if (is_array($href)) {
            foreach ($href as $k => $v) {
                if ($k == 'href' && strpos($v, '://') === false) {
                    if ($index_page === true) {
                        $link .= 'href="'.$CI->config->site_url($v).'" ';
                    } else {
                        $link .= 'href="'.$CI->config->slash_item('base_url').$v.'" ';
                    }
                } else {
                    $link .= "$k=\"$v\" ";
                }
            }

            $link .= "/>";
        } else {
            if (strpos($href, '://') !== false) {
                $link .= 'href="'.$href.'" ';
            } elseif ($index_page === true) {
                $link .= 'href="'.$CI->config->site_url($href).'" ';
            } else {
                $link .= 'href="'.$CI->config->slash_item('base_url').$href.'" ';
            }

            $link .= 'rel="'.$rel.'" type="'.$type.'" ';

            if ($media  != '') {
                $link .= 'media="'.$media.'" ';
            }

            if ($title  != '') {
                $link .= 'title="'.$title.'" ';
            }

            $link .= '/>';
        }

        return $link;
    }
}




/**
 * ul_first_last
 *
 * Generates a ul element with the first and the last elements of each list given the $first_class
 * and $last_class classes respectively.
 *
 * @access  public
 * @param   mixed
 * @return  string
 */
if (!function_exists('list_first_last')) {

    function list_first_last(
        $list,
        $type = 'ul',
        $attributes = '',
        $depth = 0,
        $first_class = 'first',
        $last_class = 'last',
        $top_li = null,
        $current = null
    ) {
        // If an array wasn't submitted there's nothing to do...
        if (!is_array($list)) {
            return $list;
        }
        if (empty($current)) {
            $current = 'home';
        }

        // Set the indentation based on the depth
        $out = str_repeat(" ", $depth);

        // Were any attributes submitted?  If so generate a string
        if (is_array($attributes)) {
            $atts = '';
            foreach ($attributes as $key => $val) {
                $atts .= ' ' . $key . '="' . $val . '"';
            }
            $attributes = $atts;
        }

        $attributes = ($depth == 0) ? $attributes : null ;

        // Write the opening list tag
        $out .= "<".$type.$attributes.">\n";

        // Cycle through the list elements.  If an array is
        // encountered we will recursively call _list()

        static $_last_list_item = '';
        $i = 0;
        $total = count($list)-1;
        foreach ($list as $key => $val) {
            $_last_list_item = $key;

            $out .= str_repeat(" ", $depth + 2);

            $class_f = ($i == 0)        ? $first_class  : null;
            $class_l = ($i == $total)   ? $last_class   : null;
            //  Work out if this li is the current one absed on $current
            if (is_array($val)) {
                $cur_val = $_last_list_item."\n";
            } else {
                $cur_val = $val;
            }

            $cur = (preg_match('/href="\/'.$current.'/i', $cur_val)) ? ' current' : null;
            $out .= "<li class=\"{$top_li} {$class_f}{$class_l}{$cur}\">";

            if (!is_array($val)) {
                $out .= $val;
            } else {
                $out .= $_last_list_item."\n";
                $out .= list_first_last($val, $type, '', $depth + 4, $first_class, $last_class);
                $out .= str_repeat(" ", $depth + 2);
            }

            $out .= "</li>\n";

            $i++;
        }

        // Set the indentation for the closing tag
        $out .= str_repeat(" ", $depth);

        // Write the closing list tag
        $out .= "</".$type.">\n";

        return $out;

    }
}

// --------------------------------------------------------------------------

//  Include the CodeIgniter original
include FCPATH . 'vendor/rogeriopradoj/codeigniter/system/helpers/html_helper.php';
