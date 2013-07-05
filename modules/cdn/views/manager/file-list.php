<?php

	echo '<li class="file list" data-title="' . $object->filename_display . '" data-id="' . $object->id . '">';
	
		echo '<div class="filename">';
		switch ( $object->mime ) :
		
			case 'image/jpg' :
			case 'image/jpeg' :
			case 'image/png' :
			case 'image/gif' :
			
				echo img( cdn_thumb( $bucket->slug, $object->filename, 30, 30 ) );
				$_action_download = 'View';
			
			break;
			
			default :
			
				$_action_download = 'Download';
			
			break;
		
		endswitch;
		
		echo $object->filename_display;
		
		echo '</div>';
		
		echo '<div class="type">' . $object->mime . '</div>';
		
		echo '<div class="filesize">' . $object->filesize . '</div>';
		
		echo '<div class="modified">' . user_date( $object->modified ) . '</div>';
		
		echo '<div class="actions">';
		
			echo '<a href="#" data-bucket="' . $bucket->slug .'" data-file="' . $object->filename .'" class="awesome green small insert">Insert</a>';
			echo anchor( 'cdn/manager/delete/' . $object->id . '?' . $_SERVER['QUERY_STRING'], 'Delete', 'class="awesome red small delete"' );
			echo '<a href="' . cdn_serve( $bucket->slug, $object->filename ) . '" class="fancybox awesome small">' . $_action_download . '</a>';
		
		echo '</div>';
		
		// --------------------------------------------------------------------------
		
		echo '<div class="clear"></div>';
		
	echo '</li>';