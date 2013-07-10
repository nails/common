<?php

//	Renders a block
if ( ! function_exists( 'cms_render_block' ) )
{
	function cms_render_block( $slug, $lang = NULL )
	{
		//	Load the model if it's not already loaded
		if ( ! get_instance()->load->model_is_loaded( 'cms_block' ) ) :
		
			get_instance()->load->model( 'cms/cms_block_model', 'cms_block' );
		
		endif;
		
		// --------------------------------------------------------------------------
		
		$_block = get_instance()->cms_block->get_by_slug( $slug );
		
		if ( ! $_block )
			return FALSE;
			
		// --------------------------------------------------------------------------
		
		if ( is_null( $lang ) || $lang == APP_DEFAULT_LANG_SLUG )
			return $_block->default_value;
		
		// --------------------------------------------------------------------------
		
		for ( $i=0; $i<count( $_block->translations ); $i++ ) :
		
			if ( $lang == $_block->translations[$i]->lang->slug ) :
			
				return $_block->translations[$i]->value;
			
			endif;
		
		endfor;
		
		return $_block->default_value;
	}
}