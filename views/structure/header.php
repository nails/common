<?php

	/**
	 * --------------------------------------------------------------------------
	 * HEADER CONTROLLER
	 * --------------------------------------------------------------------------
	 * 
	 * This view controls which header should be rendered. It will use the URI to
	 * determine the appropriate header file (against the header config file).
	 * 
	 * Override this automatic behaviour by specifying the header_override
	 * variable in the data supplied to the view.
	 * 
	 **/
	 
	 
	// --------------------------------------------------------------------------
	
		
	if ( isset( $header_override ) ) :
	
		//	Manual override
		$this->load->view( $header_override );
	
	else :

		//	Auto-detect header if there is a config file
		if ( file_exists( FCPATH . APPPATH . 'config/header_views.php' ) ) :
		
			$this->config->load( 'header_views' );
			$_match = FALSE;
			$_uri_string = $this->uri->uri_string();
			
			if ( ! $_uri_string ) :
			
				//	We're at the homepage, get the name of the default controller
				$_uri_string = $this->router->routes['default_controller'];
			
			endif;
			
			if ( $this->config->item( 'alt_header' ) ) :
			
				foreach ( $this->config->item( 'alt_header' ) AS $pattern => $template ) :
				
					//	Prep the regex
					$_key = str_replace( ':any', '.*', str_replace( ':num', '[0-9]*', $pattern ) );
					
					//	Match found?
					if ( preg_match( '#^' . $_key . '$#', $_uri_string ) ) :
					
						$_match = $template;
						break;
					
					endif;
					
				endforeach;
				
			endif;
			
			//	Load the appropriate header view
			if ( $_match ) :
			
				$this->load->view( $_match );
			
			else :
	
				$this->load->view( $this->config->item( 'default_header' ) );
			
			endif;
			
		else :
		
			//	No config file, fall back to the default Nails. header
			$this->load->view( 'structure/header/nailsdefault' );
		
		endif;
			
	endif;