<?php

/**
 * Modular Extensions HMVC
 *
 * @package     Nails
 * @subpackage  MX
 * @author      wiredesignz
 * @link        https://bitbucket.org/wiredesignz/codeigniter-modular-extensions-hmvc
 */

namespace Nails\Common\CodeIgniter\Core;

use MX_Router;

/* load the MX Router class */
require NAILS_COMMON_PATH . 'MX/Router.php';

class Router extends MX_Router
{
    public function current_module()
    {
        return $this->module;
    }

    // --------------------------------------------------------------------------

    /**
     * Extending method purely to change the 404 behaviour and PSR-2 things a little.
     *
     * When show404() is reached it means that a valid controller could not be
     * found. These errors should be logged, however show404() by default doesn't
     * log errors, hence the override.
     *
     * @param  array $segments The URI segments
     *
     * @return array
     */
    protected function _validate_request($segments)
    {
        if (count($segments) == 0) {
            return $segments;
        }

        /* locate module controller */
        if ($located = $this->locate($segments)) {
            return $located;
        }

        /* use a default 404_override controller */
        if (isset($this->routes['404_override']) && $this->routes['404_override']) {
            $segments = explode('/', $this->routes['404_override']);

            if ($located = $this->locate($segments)) {
                return $located;
            }
        }

        /* no controller found */
        show404('', true);
    }
}
