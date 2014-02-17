<?php

	//	Set defaults
	$_image_id	= ! empty( $image_id )	? $image_id	: '';
	$_scaling	= ! empty( $scaling )	? $scaling	: '';
	$_width		= ! empty( $width )		? $width	: '';
	$_height	= ! empty( $height )	? $height	: '';
	$_linking	= ! empty( $linking )	? $linking	: '';
	$_url		= ! empty( $url )		? $url		: '';
	$_target	= ! empty( $target )	? $target	: '';
	$_link_attr	= ! empty( $link_attr )	? $link_attr	: '';
	$_img_attr	= ! empty( $img_attr )	? $img_attr	: '';


	if ( $_image_id ) :

		//	Determine image URL
		if ( $_scaling == 'CROP' && $_width && $_height ) :

			$_img_url = cdn_thumb( $_image_id, $_width, $_height );

		elseif ( $_scaling == 'SCLAE' && $_width && $_height ) :

			$_img_url = cdn_scale( $_image_id, $_width, $_height );

		else :

			$_img_url = cdn_serve( $_image_id );

		endif;

		// --------------------------------------------------------------------------

		//	Determine linking
		if ( $_linking == 'CUSTOM' && $_url ) :

			$_link_url		= $_url;
			$_link_target	= $_target ? 'target="' . $_target . '"' : '' ;

		elseif( $_linking == 'FULLSIZE' ) :

			$_link_url		= cdn_serve( $_image_id );
			$_link_target	= $_target ? 'target="' . $_target . '"' : '' ;

		else :

			$_link_url		= '';
			$_link_target	= '';

		endif;

		// --------------------------------------------------------------------------

		// Render
		$_out = '';
		$_out .= $_link_url ? '<a href="' . $_link_url . '" ' . $_link_attr . '>' : '';
		$_out .= '<img src="' . $_img_url . '" ' . $_img_attr . '/>';
		$_out .= $_link_url ? '</a>' : '';

		echo $_out;



	endif;