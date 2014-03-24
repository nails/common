<?php (defined('BASEPATH')) OR exit('No direct script access allowed');

/**
 * Modular Extensions - HMVC
 *
 * Adapted from the CodeIgniter Core Classes
 * @link	http://codeigniter.com
 *
 * Description:
 * This library extends the CodeIgniter CI_Config class
 * and adds features allowing use of modules and the HMVC design pattern.
 *
 * Install this file as application/third_party/MX/Config.php
 *
 * @copyright	Copyright (c) 2011 Wiredesignz
 * @version 	5.4
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 **/
class MX_Config extends CI_Config
{
	protected $routes_ssl;


	// --------------------------------------------------------------------------


	public function site_url( $uri = '', $force_secure = FALSE )
	{
		//	Prepare the URI as normal
		$_uri = parent::site_url( $uri );

		// --------------------------------------------------------------------------

		//	If forcing secure just return now
		if ( $force_secure ) :

			return preg_replace( '#^' . BASE_URL . '#', SECURE_BASE_URL, $_uri );

		endif;

		// --------------------------------------------------------------------------

		//	If SSL routing is enabled then parse the URL
		if ( APP_SSL_ROUTING ) :

			$_prefix = 'https://';
			$this->load( 'routes_ssl' );

			// --------------------------------------------------------------------------

			//	Fetch SSL routes
			if ( ! $this->routes_ssl ) :

				$this->routes_ssl = $this->item( 'routes_ssl' );

				if ( ! $this->routes_ssl ) :

					$this->routes_ssl = array();

				endif;

			endif;

			// --------------------------------------------------------------------------

			//	Analyse target URL, if it matches a route then change it to be an https URL
			$i = 0;
			foreach ( $this->routes_ssl AS $route ) :

				//	Swap out the pseudo regex's
				$route = str_replace( ':any', '.*', $route );
				$route = str_replace( ':num', '[0-9]*', $route );

				//	See if any of the routes match, if they do halt the loop.
				if ( preg_match( '#^' . preg_quote( $route, '#' ) . '#', $_uri ) ) :

					$i++;
					break;

				endif;

			endforeach;


			// --------------------------------------------------------------------------

			//	If there was a match replace http:// with https://; also replace any
			//	calls for anything to the assets folder or the favicon (so secure content
			//	is shown).

			//	HTTPS is considered on if the domain matches that given in SECURE_BASE_URL
			//	or if the page is being served through HTTPS

			if ( isset( $_SERVER ) ) :

				$_page_is_secure = page_is_secure();

				if (
					   ( $i )
					|| ( $_page_is_secure && preg_match( '#^' . BASE_URL . 'assets.*#', $_uri ) )
					|| ( $_page_is_secure && preg_match( '#^' . NAILS_ASSETS_URL . '.*#', $_uri ) )
					|| ( $_page_is_secure && preg_match( '#^' . BASE_URL . 'favicon\.ico#', $_uri ) )
				) :

					//	SSL is off and there was a match, turn SSL on
					$_uri = preg_replace( '#^' . BASE_URL . '#', SECURE_BASE_URL, $_uri );

				endif;

			endif;

		endif;

		// --------------------------------------------------------------------------

		//	Spit back our result
		return $_uri;
	}


	// --------------------------------------------------------------------------


	public function load($file = 'config', $use_sections = FALSE, $fail_gracefully = FALSE, $_module = '') {

		if (in_array($file, $this->is_loaded, TRUE)) return $this->item($file);

		$_module OR $_module = CI::$APP->router->fetch_module();
		list($path, $file) = Modules::find($file, $_module, 'config/');

		if ($path === FALSE)
		{
			parent::load($file, $use_sections, $fail_gracefully);

			return $this->item($file);
		}

		if ($config = Modules::load_file($file, $path, 'config')) {

			/* reference to the config array */
			$current_config =& $this->config;

			if ($use_sections === TRUE)	{

				if (isset($current_config[$file])) {
					$current_config[$file] = array_merge($current_config[$file], $config);
				} else {
					$current_config[$file] = $config;
				}

			} else {
				$current_config = array_merge($current_config, $config);
			}
			$this->is_loaded[] = $file;
			unset($config);
			return $this->item($file);
		}
	}


	// --------------------------------------------------------------------------


	/**
	 * Secure Base URL
	 * Returns secure_base_url [. uri_string]
	 *
	 * @access public
	 * @param string $uri
	 * @return string
	 */
	function secure_base_url($uri = '')
	{
		return SECURE_BASE_URL.ltrim($this->_uri_string($uri), '/');
	}
}