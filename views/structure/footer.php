<?php

	/**
	 * --------------------------------------------------------------------------
	 * FOOTER CONTROLLER
	 * --------------------------------------------------------------------------
	 *
	 * This view controls which footer should be rendered. It will use the URI to
	 * determine the appropriate footer file (against the footer config file).
	 *
	 * Override this automatic behaviour by specifying the footer_override
	 * variable in the data supplied to the view.
	 *
	 **/

	// --------------------------------------------------------------------------

	//	Catch 404 nonsense
	$_is_404 = defined( 'NAILS_IS_404' ) && NAILS_IS_404 ? TRUE : FALSE;

	// --------------------------------------------------------------------------


	if ( isset( $footer_override ) ) :

		//	Manual override
		$this->load->view( $footer_override );

	else :

		//	Auto-detect footer if there is a config file
		if ( file_exists( FCPATH . APPPATH . 'config/footer_views.php' ) ) :

			$this->config->load( 'footer_views' );
			$_match = FALSE;
			$_uri_string = $this->uri->uri_string();

			if ( ! $_uri_string ) :

				//	We're at the homepage, get the name of the default controller
				$_uri_string = $this->router->routes['default_controller'];

			endif;

			if ( $this->config->item( 'alt_footer' ) ) :

				foreach ( $this->config->item( 'alt_footer' ) AS $pattern => $template ) :

					//	Prep the regex
					$_key = str_replace( ':any', '.*', str_replace( ':num', '[0-9]*', $pattern ) );

					//	Match found?
					if ( preg_match( '#^' . preg_quote( $_key, '#' ) . '$#', $_uri_string ) ) :

						$_match = $template;
						break;

					endif;

				endforeach;

			endif;

			//	Load the appropriate footer view
			if ( $_match ) :

				$this->load->view( $_match );

			elseif ( $this->uri->segment( 1 ) == 'admin' ) :

				//	No match, but in admin, load the appropriate admin view
				if ( $_is_404 ) :

					//	404 with no route, show the default footer
					$this->load->view( $this->config->item( 'default_footer' ) );

				else :

					//	Admin has no route and it's not a 404, load up the Nails admin footer
					$this->load->view( 'structure/footer/nails-admin' );

				endif;

			else :

				$this->load->view( $this->config->item( 'default_footer' ) );

			endif;

		elseif ( $this->uri->segment( 1 ) == 'admin' && empty( $_is_404 ) ) :

			//	Loading admin footer and no config file. This isn't a 404 so
			//	go ahead and load the normal Nails admin footer

			$this->load->view( 'structure/footer/nails-admin' );

		elseif ( file_exists( FCPATH . APPPATH . 'views/structure/footer/default.php' ) ) :

			//	No config file, but the app has a default footer
			$this->load->view( 'structure/footer/default' );

		else :

			//	No config file or app default, fall back to the default Nails. footer
			$this->load->view( 'structure/footer/nails-default' );

		endif;

	endif;