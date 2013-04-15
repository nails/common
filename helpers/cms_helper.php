<?php

//	Fetches the information about the current page from the DB
if ( ! function_exists( 'get_page' ) )
{
	function get_page()
	{
		$_ci =& get_instance();
		
		// --------------------------------------------------------------------------
		
		return $_ci->page_model->get_page();
	}
}


// --------------------------------------------------------------------------


//	Renders a page
if ( ! function_exists( 'render_page' ) )
{
	function render_page( $data = NULL)
	{
		$_ci =& get_instance();
		
		// --------------------------------------------------------------------------
		
		//	Fetch the data for this page
		$_page = get_page();
		
		if ( ! $_page )
			show_404();
			
		// --------------------------------------------------------------------------
		
		//	Load the views
		if ( $data === NULL ) :
		
			$_userobject =& get_userobject();
			
			// --------------------------------------------------------------------------
			
			//	Catch any flashdata
			
			// --------------------------------------------------------------------------
			
			//	Compile the view data
			$_data = array(
				'user'		=> &$_userobject,
				'page'		=> &$_page,
				'error'		=> $_ci->session->flashdata( 'error' ),
				'success'	=> $_ci->session->flashdata( 'success' ),
				'notice'	=> $_ci->session->flashdata( 'notice' ),
				'message'	=> $_ci->session->flashdata( 'message' )
			);
			
			// --------------------------------------------------------------------------
			
			$_ci->load->view( 'structure/header',			$_data );
			$_ci->load->view( 'templates/' . $_page->type,	$_data );
			$_ci->load->view( 'structure/footer',			$_data );
			
		else :
		
			$data['page'] = $_page;
			
			$_ci->load->view( 'structure/header',			$data );
			$_ci->load->view( 'templates/' . $_page->type,	$data );
			$_ci->load->view( 'structure/footer',			$data );

		
		endif;
	}
}


// --------------------------------------------------------------------------


//	Renders a block
if ( ! function_exists( 'cms_render_block' ) )
{
	function cms_render_block( $slug, $lang = NULL )
	{
		//	Load the model if it's not already loaded
		if ( ! get_instance()->load->model_is_loaded( 'cms_block' ) ) :
		
			get_instance()->load->model( 'cms_block_model', 'cms_block' );
		
		endif;
		
		// --------------------------------------------------------------------------
		
		$_block = get_instance()->cms_block->get_by_slug( $slug );
		
		if ( ! $_block )
			return FALSE;
			
		// --------------------------------------------------------------------------
		
		if ( is_null( $lang ) || $lang == APP_DEFAULT_LANG_SAFE )
			return $_block->default_value;
		
		// --------------------------------------------------------------------------
		
		for ( $i=0; $i<count( $_block->translations ); $i++ ) :
		
			if ( $lang == $_block->translations[$i]->lang->safename ) :
			
				return $_block->translations[$i]->value;
			
			endif;
		
		endfor;
		
		return $_block->default_value;
	}
}


// --------------------------------------------------------------------------


//	Renders a page
if ( ! function_exists( 'render_widget' ) )
{
	function render_widget( $widget , $extra_classes = '' )
	{
		$_ci	=& get_instance();
		$_out	= '';
		
		// --------------------------------------------------------------------------
		
		//	Determine if the target page is accessible to the current user
		$_locked = '';
		if ( $widget->page_id ) :
		
			$_can_access = active_user_can_access( $widget->can_access_public, $widget->can_access_account, $widget->can_access_employment, $widget->can_access_company );
			
			if ( $_can_access != 'PUBLIC' && $_can_access != 'OK' ) :
			
				$_locked = 'locked';
			
			endif;
			
		endif;
		
		// --------------------------------------------------------------------------
		
		//	Start rendering the widget
		$_out  = '<div class="' . $_locked . ' ' . $extra_classes . ' columns widget ' . $widget->override_classes . '" data-id="' . $widget->id . '">';
		$_out .= isset( $widget->link )		&& $widget->link	? '<a href="' . site_url( $widget->link ) . '">' : NULL;
		$_out .= isset( $widget->image )	&& $widget->image	? '<img src="' . cdn_scale( 'widgets', $widget->image, 220, 300 ) . '" class="scale-with-grid" width="220" height="165">' : NULL;
		$_out .= isset( $widget->link )		&& $widget->link	? '</a>' : NULL;
		
		$_out .= isset( $widget->title )	&& $widget->title	? '<h4>' : NULL;
		$_out .= isset( $widget->link )		&& $widget->link	? '<a href="' . site_url( $widget->link ) . '">' : NULL;
		$_out .= isset( $widget->title )	&& $widget->title	? $widget->title : NULL;
		$_out .= isset( $widget->link )		&& $widget->link	? '</a>' : NULL;
		$_out .= isset( $widget->title )	&& $widget->title	? '</h4>' : NULL;
		
		if ( $_locked && ( isset( $widget->link ) && $widget->link ) ) :
		
			switch ( $_can_access ) :
			
				case 'REQUIRE_EMPLOYMENT' :
				
					$_out .= '<p class="locked">You need to be registered for employment services to access this page. Click ' . anchor( 'my-account/employment', 'here' ) .' to register your account for employment services.</p>';
				
				break;
				
				case 'NO_ACCESS' :
				
					$_out .= '<p class="locked">Sorry, your account does not have access to this page.</p>';
				
				break;
				
				case 'REQUIRE_LOGIN' :
				default :
				
					$_return_to = isset( $widget->link ) && $widget->link ? '?return_to=' . $widget->link : '';
					$_out .= '<p class="locked">You must be logged in to view this page. Please click here to ' . anchor( 'auth/login' . $_return_to, 'login' ) . ' or ' . anchor( 'register', 'register' ) .'.</p>';
				
				break;
			
			endswitch;
		
		endif;
		
		$_out .= isset( $widget->body )		&& $widget->body	? auto_typography( $widget->body ) : NULL;
		
		if ( isset( $widget->link ) && $widget->link ) :
		
			$_out .= '<p>' . anchor( $widget->link, $widget->verb ) . '</p>';
		
		endif;
		
		$_out .= '</div>';
		
		// --------------------------------------------------------------------------
		
		return $_out;
	}
}

// --------------------------------------------------------------------------


//	Checks permissions against the active user
if ( ! function_exists( 'active_user_can_access' ) )
{
	function active_user_can_access( $public, $account, $employment, $company )
	{
		if ( $public ) :
		
			//	Publicaly available
			$_can_access = 'PUBLIC';
		
		else :
		
			//	Need to be logged in
			if ( ! get_userobject()->is_logged_in() ) :
			
				$_can_access = 'REQUIRE_LOGIN';
			
			else :
			
				//	Check if this user can access this page
				$_can_access = 'NO_ACCESS';
				
				switch( active_user( 'group_id' ) ) :
				
					//	Super users and admins can view
					case '1' :
					case '2' :
					
						$_can_access = 'OK';
					
					break;
					
					// --------------------------------------------------------------------------
					
					case '3':
					
						//	Can members access this page?
						if ( active_user( 'is_registered_for_employment' ) ) :
						
							//	These people can access both types
							if ( $account || $employment ) :
							
								$_can_access = 'OK';
							
							else :
							
								$_can_access = 'NO_ACCESS';
							
							endif;
						
						else :
						
							if ( $account ) :
							
								$_can_access = 'OK';
							
							else :
							
								//	If you can view this with an employment account, redirect differently
								if ( $employment ) :
								
									$_can_access = 'REQUIRE_EMPLOYMENT';
								
								else :
								
									$_can_access = 'NO_ACCESS';
									
								endif;
							
							endif;
						
						endif;
					
					break;
					
					// --------------------------------------------------------------------------
					
					case '4':
					
						if ( $company ) :
						
							$_can_access = 'OK';
						
						else :
						
							$_can_access = 'NO_ACCESS';
						
						endif;
					
					break;
					
					// --------------------------------------------------------------------------
					
					default :
					
						//	Not sure how to handle this, error and homepage
						$_can_access = 'NO_ACCESS';
					
					break;
				
				endswitch;
			
			endif;
		
		endif;
		
		// --------------------------------------------------------------------------
		
		return $_can_access;
	}
}