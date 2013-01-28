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
					if ( preg_match( '#^' . $_key . '$#', $_uri_string ) ) :
					
						$_match = $template;
						break;
					
					endif;
					
				endforeach;
				
			endif;
			
			//	Load the appropriate footer view
			if ( $_match ) :
			
				$this->load->view( $_match );
			
			else :
	
				$this->load->view( $this->config->item( 'default_footer' ) );
			
			endif;
			
		else :
		
			//	No config file, fall back to the default Nails. footer
			$this->load->view( 'structure/footer/nailsdefault' );
		
		endif;
			
	endif;