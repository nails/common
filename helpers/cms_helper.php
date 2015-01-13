<?php

//	Renders a block
if ( ! function_exists( 'cms_render_block' ) )
{
	function cms_render_block( $slug, $lang = NULL )
	{
		//	Load the model if it's not already loaded
		if ( ! get_instance()->load->isModelLoaded( 'cms_block_model' ) ) :

			get_instance()->load->model( 'cms/cms_block_model' );

		endif;

		// --------------------------------------------------------------------------

		$_block = get_instance()->cms_block_model->get_by_slug( $slug );

		if ( ! $_block ) :

			return FALSE;

		endif;

		// --------------------------------------------------------------------------

		if ( NULL === $lang || $lang == APP_DEFAULT_LANG_CODE ) :

			return $_block->default_value;

		endif;

		// --------------------------------------------------------------------------

		for ( $i=0; $i<count( $_block->translations ); $i++ ) :

			if ( $lang == $_block->translations[$i]->lang->slug ) :

				return $_block->translations[$i]->value;

			endif;

		endfor;

		return $_block->default_value;
	}
}