<?php

class NAILS_Routes_model extends NAILS_Model
{
    protected $canWriteRoutes;
    protected $routesDir;
    protected $routesFile;
    protected $routeWriters;
    protected $routes;

    public $cant_write_reason;

    // --------------------------------------------------------------------------

    /**
     * Constructs the model
     */
    public function __construct()
    {
        parent::__construct();

        // --------------------------------------------------------------------------

        //  Set Defaults
        $this->routesDir      = DEPLOY_CACHE_DIR;
        $this->routesFile     = 'routes_app.php';
        $this->routeWriters   = array();
        $this->canWriteRoutes = null;
        $this->routes         = array();

        if (!$this->canWriteRoutes()) {

            $this->cant_write_reason = $this->last_error();
            $this->clear_errors();
        }

        //  Default writers
        $this->routeWriters['sitemap'] = array($this, 'routesSitemap');
        $this->routeWriters['cms']     = array($this, 'routesCms');
        $this->routeWriters['shop']    = array($this, 'routesShop');
        $this->routeWriters['blog']    = array($this, 'routesBlog');
    }

    // --------------------------------------------------------------------------

    /**
     * Update routes
     * @param  string $which For which model to restrict the route update
     * @return boolean
     */
    public function update($which = NULL)
    {
        if (!$this->canWriteRoutes) {

            $this->_set_error($this->cant_write_reason);
            return false;
        }

        // --------------------------------------------------------------------------

        $this->_data = '<?php  if (!defined(\'BASEPATH\')) exit(\'No direct script access allowed\');' . "\n\n";
        $this->_data .= '// THIS FILE IS CREATED/MODIFIED AUTOMATICALLY, ANY MANUAL EDITS WILL BE OVERWRITTEN'."\n\n";

        foreach ($this->routeWriters as $slug => $method) {

            /**
             * @TODO: Give the ability to selectively update a part of the routes file.
             * Perhaps restricting edits to be between two known comments...?
             */

            // if (NULL == $which || $which == $slug) {

                if (is_callable(array($method[0], $method[1]))) {

                    $_result = call_user_func(array($method[0], $method[1]));

                    if (is_array($_result)) {

                        $this->routes = array_merge($this->routes, $_result);
                    }
                }
            // }
        }

        // --------------------------------------------------------------------------

        //  Start writing the file
        return $this->_write_file();
    }

    // --------------------------------------------------------------------------

    /**
     * Get all Sitemap routes
     * @return array
     */
    protected function routesSitemap()
    {
        $routes = array();

        if (isModuleEnabled('sitemap')) {

            $this->load->model('sitemap/sitemap_model');

            $routes['//BEGIN SITEMAP'] = '';
            $routes = $routes + $this->sitemap_model->get_routes();
            $routes['//END SITEMAP'] = '';
        }

        return $routes;
    }

    // --------------------------------------------------------------------------

    /**
     * Get all CMS routes
     * @return array
     */
    protected function routesCms()
    {
        $routes = array();

        if (isModuleEnabled('cms')) {

            $routes['//BEGIN CMS'] = '';

            // --------------------------------------------------------------------------

            $this->load->model('cms/cms_page_model');
            $_pages = $this->cms_page_model->get_all();

            foreach ($_pages as $page) {

                if ($page->is_published) {

                    $routes[$page->published->slug] = 'cms/render/page/' . $page->id;
                }
            }

            // --------------------------------------------------------------------------

            /**
             *  Make a route for each slug history item, don't overwrite any existing route
             *  Doing them second and checking (instead of letting the real pages overwrite
             *  the key) in an attempt to optimise, the router takes the first route it comes
             *  across so, the logic is that the "current" slug is the one which is getting
             *  hit most often, so place it first, if a legacy slug is used (in theory less
             *  often) then the router can work a little harder.
             **/

            $this->db->select('sh.slug,sh.page_id');
            $this->db->join(NAILS_DB_PREFIX . 'cms_page p', 'p.id = sh.page_id');
            $this->db->where('p.is_deleted', false);
            $this->db->where('p.is_published', true);
            $_slugs = $this->db->get(NAILS_DB_PREFIX . 'cms_page_slug_history sh')->result();

            foreach ($_slugs AS $route) {

                if (!isset($routes[$route->slug])) {

                    $routes[$route->slug] = 'cms/render/legacy_slug/' . $route->page_id;
                }
            }

            // --------------------------------------------------------------------------

            $routes['//END CMS'] = '';
        }

        return $routes;
    }

    // --------------------------------------------------------------------------

    /**
     * Get all Shop routes
     * @return array
     */
    protected function routesShop()
    {
        $routes = array();

        if (isModuleEnabled('shop')) {

            $_settings = app_setting(NULL, 'shop', true);

            $routes['//BEGIN SHOP'] = '';

            //  Shop front page route
            $_url = isset($_settings['url']) ? substr($_settings['url'], 0, -1) : 'shop';
            $routes[$_url . '(/(.+))?'] = 'shop/$2';

            //  @TODO: all shop product/category/tag/sale routes etc

            $routes['//END SHOP'] = '';
        }

        return $routes;
    }

    // --------------------------------------------------------------------------

    /**
     * Get all Blog routes
     * @return array
     */
    protected function routesBlog()
    {
        $routes = array();

        if (isModuleEnabled('blog')) {

            $this->load->model('blog/blog_model');
            $_blogs = $this->blog_model->get_all();

            $routes['//BEGIN BLOG'] = '';
            foreach ($_blogs as $blog) {

                $_settings = app_setting(NULL, 'blog-' . $blog->id, true);

                //  Blog front page route
                $_url = isset($_settings['url']) ? substr($_settings['url'], 0, -1) : 'blog';
                $routes[$_url . '(/(.+))?'] = 'blog/' . $blog->id . '/$2';
            }
            $routes['//END BLOG'] = '';
        }

        return $routes;
    }

    // --------------------------------------------------------------------------

    /**
     * Write the routes file
     * @return boolean
     */
    protected function _write_file()
    {
        //  Routes are writeable, apparently, give it a bash
        $_data = '<?php  if (!defined(\'BASEPATH\')) exit(\'No direct script access allowed\');' . "\n\n";
        $_data .= '/**' . "\n";
        $_data .= ' * THIS FILE IS CREATED/MODIFIED AUTOMATICALLY'."\n";
        $_data .= ' * ==========================================='."\n";
        $_data .= ' *'."\n";
        $_data .= ' * Any changes you make in this file will be overwritten the' . "\n";
        $_data .= ' * next time the app routes are generated.'."\n";
        $_data .= ' *'."\n";
        $_data .= ' * See Nails docs for instructions on how to utilise the' . "\n";
        $_data .= ' * routes_app.php file'."\n";
        $_data .= ' *'."\n";
        $_data .= ' **/' . "\n\n";

        // --------------------------------------------------------------------------

        foreach ($this->routes as $key => $value) {

            if (preg_match('#^//.*$#', $key)) {

                //  This is a comment
                $_data .= $key . "\n";

            } else {

                //  This is a route
                $_data .= '$route[\'' . $key . '\']=\'' . $value . '\';' . "\n";
            }
        }

        $_data .= "\n" . '//LAST GENERATED: ' . date('Y-m-d H:i:s');

        // --------------------------------------------------------------------------

        $_fh = @fopen($this->routesDir . $this->routesFile, 'w');

        if (!$_fh) {

            $this->_set_error('Unable to open routes file for writing.<small>Located at: ' . $this->routesDir . $this->routesFile . '</small>');
            return false;
        }

        if (!fwrite($_fh, $_data)) {

            fclose($_fh);
            $this->_set_error('Unable to write data to routes file.<small>Located at: ' . $this->routesDir . $this->routesFile . '</small>');
            return false;
        }

        fclose($_fh);

        return true;
    }

    // --------------------------------------------------------------------------

    /**
     * Determine whether or not the routes can be written
     * @return boolean
     */
    protected function canWriteRoutes()
    {
        if (!is_null($this->canWriteRoutes)) {

            return $this->canWriteRoutes;
        }

        //  First, test if file exists, if it does is it writable?
        if (file_exists($this->routesDir . $this->routesFile)) {

            if (is_really_writable($this->routesDir . $this->routesFile)) {

                $this->canWriteRoutes = true;
                return true;

            } else {

                //  Attempt to chmod the file
                if (@chmod($this->routesDir . $this->routesFile, FILE_WRITE_MODE)) {

                    $this->canWriteRoutes = true;
                    return true;

                } else {

                    $this->_set_error('The route config exists, but is not writeable. <small>Located at: ' . $this->routesDir . $this->routesFile . '</small>');
                    $this->canWriteRoutes = false;
                    return false;
                }
            }

        } elseif (is_really_writable($this->routesDir)) {

            $this->canWriteRoutes = true;
            return true;

        } else {

            //  Attempt to chmod the directory
            if (@chmod($this->routesDir, DIR_WRITE_MODE)) {

                $this->canWriteRoutes = true;
                return true;

            } else {

                $this->_set_error('The route directory is not writeable. <small>' . $this->routesDir . '</small>');
                $this->canWriteRoutes = false;
                return false;
            }
        }
    }
}

// --------------------------------------------------------------------------

/**
 * OVERLOADING NAILS' MODELS
 *
 * The following block of code makes it simple to extend one of the core
 * models. Some might argue it's a little hacky but it's a simple 'fix'
 * which negates the need to massively extend the CodeIgniter Loader class
 * even further (in all honesty I just can't face understanding the whole
 * Loader class well enough to change it 'properly').
 *
 * Here's how it works:
 *
 * CodeIgniter instantiate a class with the same name as the file, therefore
 * when we try to extend the parent class we get 'cannot redeclare class X' errors
 * and if we call our overloading class something else it will never get instantiated.
 *
 * We solve this by prefixing the main class with NAILS_ and then conditionally
 * declaring this helper class below; the helper gets instantiated et voila.
 *
 * If/when we want to extend the main class we simply define NAILS_ALLOW_EXTENSION
 * before including this PHP file and extend as normal (i.e in the same way as below);
 * the helper won't be declared so we can declare our own one, app specific.
 *
 **/

if (!defined('NAILS_ALLOW_EXTENSION_ROUTES_MODEL')) {

    class Routes_model extends NAILS_Routes_model
    {
    }
}
