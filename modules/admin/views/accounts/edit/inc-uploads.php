<?php if ( module_is_enabled( 'cdn' ) ) : ?>
<fieldset  id="edit-user-uploads" class="uploads">
	<legend><?=lang( 'accounts_edit_upload_legend' )?></legend>
	<p>
	<?php
	
		echo '<ul>';

		if ( $user_uploads ) :

			foreach ( $user_uploads AS $file ) :
				
				echo '<li class="file">';
				
				switch( $file->mime ) :
				
					case 'image/jpeg':
					case 'image/gifgif':
					case 'image/png':
					
						echo '<a href="' . cdn_serve( $file->bucket->slug, $file->filename ) . '" class="fancybox">';
						echo img( cdn_thumb( $file->bucket->slug, $file->filename, 35, 35 ) );
						echo $file->filename_display;
						echo '</a>';
					
					break;
					
					// --------------------------------------------------------------------------
					
					default :
					
						echo anchor( cdn_serve( $file->bucket->slug, $file->filename ) . '?dl=1', $file->filename_display );
					
					break;
				
				endswitch;
				
				echo '</li>';
			
			endforeach;

		else :

			echo '<li class="no-data">' . lang( 'accounts_edit_upload_nofile' ) . '</li>';

		endif;

		echo '</ul>';
	
	?>
	</p>
</fieldset>
<?php endif;