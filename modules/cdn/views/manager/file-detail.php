<?php

	echo '<li class="file detail" data-title="' . $object->filename_display . '" data-id="' . $object->id . '">';
	
	echo '<div class="image">';
	switch ( $object->mime ) :
	
		case 'image/jpg' :
		case 'image/jpeg' :
		case 'image/png' :
		case 'image/gif' :
			
			//	Thumbnail
			echo img( cdn_scale( $bucket->slug, $object->filename, 150, 175 ) );
			
			$_action_download = 'View';
																	
		break;
		
		// --------------------------------------------------------------------------
		
		default :
		
			//	Generic file
			echo img( array( 'src' => NAILS_URL . 'img/icons/document-icon-128px.png', 'style' => 'border:none;margin-top:20px;' ) );
			
			$_action_download = 'Download';
		
		break;
	
	endswitch;
	echo '</div>';
													
	//	Filename
	echo '<div class="details">';
	
		echo '<span class="filename">' . $object->filename_display . '</span>';
		
		echo '<div class="type"><strong>Type:</strong> ' . $object->mime . '</div>';
		
		echo '<div class="filesize"><strong>Filesize:</strong> ' . $object->filesize . '</div>';
		
		echo '<div class="created"><strong>Created:</strong> ' . user_date( $object->created ) . '</div>';
		echo '<div class="modified"><strong>Modified:</strong> ' . user_date( $object->modified ) . '</div>';
		
		echo '<div class="actions">';
		
			echo '<a href="#" data-bucket="' . $bucket->slug .'" data-file="' . $object->filename .'" class="awesome green small insert">Insert</a>';
			echo anchor( 'cdn/manager/delete/' . $object->id . '?' . $_SERVER['QUERY_STRING'], 'Delete', 'class="awesome red small delete"' );
			echo '<a href="' . cdn_serve( $bucket->slug, $object->filename ) . '" class="fancybox awesome small">' . $_action_download . '</a>';
		
		echo '</div>';
		
	echo '</div>';
	
	echo '<div class="clear"></div>';
	
	echo '</li>';