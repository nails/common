<?php

/**
 * This class performs some essential method overloading
 *
 * @package     Nails
 * @subpackage  common
 * @category    errors
 * @author      Nails Dev Team
 * @link
 */

namespace Nails\Common\CodeIgniter\Core;

use CI_Uri;

class Uri extends CI_Uri
{
    /**
     * Overriding method to catch exceptions and route accordingly
     *
     * @param string $str
     */
    public function filter_uri(&$str)
    {
        try {
            parent::filter_uri($str);
        } catch (\Exception $e) {
            _NAILS_ERROR($e->getMessage());
        }
    }
}
