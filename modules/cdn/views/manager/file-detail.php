<?php

	echo '<li class="file detail" data-title="' . $object->filename_display . '" data-id="' . $object->id . '">';
	
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

	echo '</div>';
													
	//	Filename
	echo '<div class="details">';
	
		echo '<span class="filename">' . $object->filename_display . '</span>';
		
		echo '<div class="type"><strong>Type:</strong> ' . $object->mime . '</div>';
		
		echo '<div class="filesize"><strong>Filesize:</strong> ' . $object->filesize . '</div>';
		
		echo '<div class="created"><strong>Created:</strong> ' . user_datetime( $object->created ) . '</div>';
		echo '<div class="modified"><strong>Modified:</strong> ' . user_datetime( $object->modified ) . '</div>';
		echo '<div class="attachments"><strong>Attachments:</strong> ' . count( $object->attachments ) . ' ';
		echo anchor( 'cdn/manager/attachments/' . $object->id . '?' . $_SERVER['QUERY_STRING'], '<small>[details]</small>', 'class="fancybox" data-fancybox-type="iframe"' );
		echo '</div>';
		
		echo '<div class="actions">';

			//	Any restrictive attachments?
			$_total			= 0;
			$_normal		= 0;
			$_restrictive	= 0;

			foreach( $object->attachments AS $attachment ) :

				$_total++;

				if ( $attachment->is_restrictive ) :

					$_restrictive++;

				else :

					$_normal++;

				endif;

			endforeach;

			// --------------------------------------------------------------------------
		
			echo '<a href="#" data-id="' . $object->id . '" data-bucket="' . $bucket->slug .'" data-file="' . $object->filename .'" class="awesome green small insert">Insert</a>';
			echo anchor( 'cdn/manager/delete/' . $object->id . '?' . $_SERVER['QUERY_STRING'], 'Delete', 'class="awesome red small delete" data-attachments-total="' . $_total . '" data-attachments-restrictive="' . $_restrictive . '" data-attachments-normal="' . $_normal . '"' );
			echo '<a href="' . cdn_serve( $object->id ) . '" class="fancybox awesome small">' . $_action_download . '</a>';
		
		echo '</div>';
		
	echo '</div>';
	
	echo '<div class="clear"></div>';
	
	echo '</li>';