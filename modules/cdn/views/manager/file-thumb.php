<?php

	echo '<li class="file thumb" data-title="' . $object->filename_display . '" data-id="' . $object->id . '">';
	echo '<div class="image">';

		if ( $object->is_img ) :

			//	Thumbnail
			echo img( cdn_scale( $object->id, 150, 175 ) );
			$_fancybox_class	= 'cdn-fancybox';
			$_fancybox_type		= '';
			$_url				= cdn_serve( $object->id );
			$_action			= 'View';


		elseif ( $object->mime == 'audio/mpeg' ) :

			//	MP3
			echo '<span class="ion-music-note" style="font-size:14em"></span>';
			$_fancybox_class	= 'cdn-fancybox';
			$_fancybox_type		= 'iframe';
			$_url				= cdn_serve( $object->id );
			$_action			= 'Play';

		elseif ( $object->mime == 'application/pdf' ) :

			//	PDF
			echo '<span class="ion-document" style="font-size:14em"></span>';
			$_fancybox_class	= 'cdn-fancybox';
			$_fancybox_type		= 'iframe';
			$_url				= cdn_serve( $object->id );
			$_action			= 'View';

		else :

			//	Generic file, force download
			echo '<span class="ion-document" style="font-size:14em"></span>';
			$_fancybox_class	= '';
			$_fancybox_type		= '';
			$_url				= cdn_serve( $object->id, TRUE );
			$_action			= 'Download';

		endif;

		//	Actions
		echo '<div class="actions">';

			echo '<a href="#" data-fieldid="' . $this->input->get( 'fieldid' ) . '" data-id="' . $object->id . '" data-bucket="' . $bucket->slug .'" data-file="' . $object->filename .'" class="awesome green small insert">Insert</a>';
			echo anchor( site_url( 'cdn/manager/delete/' . $object->id . $_query_string, page_is_secure() ), 'Delete', 'class="awesome red small delete"' );
			echo anchor( $_url, $_action, 'data-fancybox-title="' . $object->filename_display . '" data-fancybox-type="' . $_fancybox_type . '" class="' . $_fancybox_class . ' awesome small"' );

		echo '</div>';

	echo '</div>';

	//	Filename
	echo '<p class="filename">' . $object->filename_display . '</p>';
	echo '</li>';