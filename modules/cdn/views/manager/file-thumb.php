<?php

	echo '<li class="file thumb" data-title="' . $object->filename_display . '" data-id="' . $object->id . '">';
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
	
		//	Actions
		echo '<div class="actions">';
		
		echo '<a href="#" data-bucket="' . $bucket->slug .'" data-file="' . $object->filename .'" class="awesome green small insert">Insert</a>';
		echo anchor( 'cdn/manager/delete/' . $object->id . '?' . $_SERVER['QUERY_STRING'], 'Delete', 'class="awesome red small delete"' );
		echo '<a href="' . cdn_serve( $bucket->slug, $object->filename ) . '" class="fancybox awesome small">' . $_action_download . '</a>';
		
		echo '</div>';
	
	echo '</div>';
													
	//	Filename
	echo '<p class="filename">' . $object->filename_display . '</p>';
	echo '</li>';