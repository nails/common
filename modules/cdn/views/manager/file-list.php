<?php

	echo '<tr class="file list" data-title="' . $object->filename_display . '" data-id="' . $object->id . '">';

		echo '<td class="filename">';

		if ( $object->is_img ) :

			//	Thumbnail
			echo img( array( 'src' => cdn_thumb( $object->id, 30, 30 ), 'class' => 'icon' ) );
			$_fancybox_class	= 'cdn-fancybox';
			$_fancybox_type		= '';
			$_url				= cdn_serve( $object->id );
			$_action			= 'View';


		elseif ( $object->mime == 'audio/mpeg' ) :

			//	PDF
			echo '<div class="icon"><span class="ion-music-note" style="font-size:2.2em"></span></div>';
			$_fancybox_class	= 'cdn-fancybox';
			$_fancybox_type		= 'iframe';
			$_url				= cdn_serve( $object->id );
			$_action			= 'Play';


		elseif ( $object->mime == 'application/pdf' ) :

			//	PDF
			echo '<div class="icon"><span class="ion-document" style="font-size:2.2em"></span></div>';
			$_fancybox_class	= 'cdn-fancybox';
			$_fancybox_type		= 'iframe';
			$_url				= cdn_serve( $object->id );
			$_action			= 'View';

		else :

			//	Generic file, force download
			echo '<div class="icon"><span class="ion-document" style="font-size:2.2em"></span></div>';
			$_fancybox_class	= '';
			$_fancybox_type		= '';
			$_url				= cdn_serve( $object->id, TRUE );
			$_action			= 'Download';

		endif;

			echo $object->filename_display;

		echo '</td>';

		echo '<td class="mime">' . $object->mime . '</td>';

		echo '<td class="filesize">' . format_bytes( $object->filesize ) . '</td>';

		echo '<td class="modified">' . user_datetime( $object->modified ) . '</td>';

		echo '<td class="actions">';

			echo '<a href="#" data-fieldid="' . $this->input->get( 'fieldid' ) . '" data-id="' . $object->id . '" data-bucket="' . $bucket->slug .'" data-file="' . $object->filename .'" class="awesome green small insert">Insert</a>';
			echo anchor( site_url( 'cdn/manager/delete/' . $object->id . '?' . $_SERVER['QUERY_STRING'], page_is_secure() ), 'Delete', 'class="awesome red small delete"' );
			echo anchor( $_url, $_action, 'data-fancybox-title="' . $object->filename_display . '" data-fancybox-type="' . $_fancybox_type . '" class="' . $_fancybox_class . ' awesome small"' );

		echo '</td>';

	echo '</tr>';