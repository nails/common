<?php

	echo '<li class="file thumb" data-title="' . $object->filename_display . '" data-id="' . $object->id . '">';
	echo '<div class="image">';

		if ( $object->is_img ) :

			//	Thumbnail
			echo img( cdn_scale( $object->id, 150, 175 ) );
			$_action_download = 'View';

		else :

			//	Generic file
			echo img( array( 'src' => NAILS_ASSETS_URL . 'img/icons/document-icon-128px.png', 'style' => 'border:none;margin-top:20px;' ) );
			$_action_download = 'Download';

		endif;

		//	Actions
		echo '<div class="actions">';

			echo '<a href="#" data-id="' . $object->id . '" data-bucket="' . $bucket->slug .'" data-file="' . $object->filename .'" class="awesome green small insert">Insert</a>';
			echo anchor( site_url( 'cdn/manager/delete/' . $object->id . '?' . $_SERVER['QUERY_STRING'], page_is_secure() ), 'Delete', 'class="awesome red small delete"' );
			echo anchor( cdn_serve( $object->id ), $_action_download, 'class="fancybox awesome small"' );

		echo '</div>';

	echo '</div>';

	//	Filename
	echo '<p class="filename">' . $object->filename_display . '</p>';
	echo '</li>';