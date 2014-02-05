<?php

	echo '<li class="file detail" data-title="' . $object->filename_display . '" data-id="' . $object->id . '">';

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

	echo '</div>';

	//	Filename
	echo '<div class="details">';

		echo '<span class="filename">' . $object->filename_display . '</span>';

		echo '<div class="type"><strong>Type:</strong> ' . $object->mime . '</div>';

		echo '<div class="filesize"><strong>Filesize:</strong> ' . format_bytes( $object->filesize ) . '</div>';

		echo '<div class="created"><strong>Created:</strong> ' . user_datetime( $object->created ) . '</div>';
		echo '<div class="modified"><strong>Modified:</strong> ' . user_datetime( $object->modified ) . '</div>';

		echo '<div class="actions">';

			echo '<a href="#" data-id="' . $object->id . '" data-bucket="' . $bucket->slug .'" data-file="' . $object->filename .'" class="awesome green small insert">Insert</a>';
			echo anchor( site_url( 'cdn/manager/delete/' . $object->id . '?' . $_SERVER['QUERY_STRING'], page_is_secure() ), 'Delete', 'class="awesome red small delete"' );
			echo '<a href="' . cdn_serve( $object->id ) . '" class="fancybox awesome small">' . $_action_download . '</a>';

		echo '</div>';

	echo '</div>';

	echo '<div class="clear"></div>';

	echo '</li>';