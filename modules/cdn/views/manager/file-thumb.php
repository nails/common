<?php

	echo '<li class="file thumb" data-title="' . $object->filename_display . '" data-id="' . $object->id . '">';
	echo '<div class="image">';

		if ( $object->is_img ) :

			//	Thumbnail
			echo img( cdn_scale( $object->id, 150, 175 ) );
			$_action_download = 'View';

		else :

			//	Generic file
			echo img( array( 'src' => NAILS_URL . 'img/icons/document-icon-128px.png', 'style' => 'border:none;margin-top:20px;' ) );
			$_action_download = 'Download';

		endif;
	
		//	Actions
		echo '<div class="actions">';

			//	Any restrictive attachments?
			$_total			= 0;
			$_normal		= 0;
			$_restrictive	= 0;

			foreach( $object->attachments AS $attachment ) :

				$_total++;

				//	TODO: Create a config file for defining this stuff
				if ( 1==0 && $attachment->is_restrictive ) :

					$_restrictive++;

				else :

					$_normal++;

				endif;

			endforeach;

			// --------------------------------------------------------------------------
		
			echo '<a href="#" data-id="' . $object->id . '" data-bucket="' . $bucket->slug .'" data-file="' . $object->filename .'" class="awesome green small insert">Insert</a>';
			echo anchor( 'cdn/manager/delete/' . $object->id . '?' . $_SERVER['QUERY_STRING'], 'Delete', 'class="awesome red small delete" data-attachments-total="' . $_total . '" data-attachments-restrictive="' . $_restrictive . '" data-attachments-normal="' . $_normal . '"' );
			echo anchor( cdn_serve( $object->id ), $_action_download, 'class="fancybox awesome small"' );
			echo anchor( 'cdn/manager/attachments/' . $object->id . '?' . $_SERVER['QUERY_STRING'], 'Attachments (' . count( $object->attachments ) . ')', 'class="fancybox awesome small" data-fancybox-type="iframe"' );
		
		echo '</div>';
	
	echo '</div>';
													
	//	Filename
	echo '<p class="filename">' . $object->filename_display . '</p>';
	echo '</li>';