<?php

	echo '<li class="file list" data-title="' . $object->filename_display . '" data-id="' . $object->id . '">';
	
		echo '<div class="filename">';

			if ( $object->is_img ) :

				echo img( cdn_thumb( $object->id, 30, 30 ) );
				$_action_download = 'View';

			else :

				$_action_download = 'Download';

			endif;
		
			echo $object->filename_display;
		
		echo '</div>';
		
		echo '<div class="type">' . $object->mime . '</div>';
		
		echo '<div class="filesize">' . $object->filesize . '</div>';
		
		echo '<div class="modified">' . user_date( $object->modified ) . '</div>';
		
		echo '<div class="actions">';
		
			echo '<a href="#" data-id="' . $object->id . '" data-bucket="' . $bucket->slug .'" data-file="' . $object->filename .'" class="awesome green small insert">Insert</a>';
			echo anchor( 'cdn/manager/delete/' . $object->id . '?' . $_SERVER['QUERY_STRING'], 'Delete', 'class="awesome red small delete" data-attachments="' . count( $object->attachments ) . '"' );
			echo anchor( cdn_serve( $object->id ), $_action_download, 'class="fancybox awesome small"' );
			echo anchor( 'cdn/manager/attachments/' . $object->id . '?' . $_SERVER['QUERY_STRING'], 'Attachments (' . count( $object->attachments ) . ')', 'class="fancybox awesome small" data-fancybox-type="iframe"' );
		
		echo '</div>';
		
		// --------------------------------------------------------------------------
		
		echo '<div class="clear"></div>';
		
	echo '</li>';