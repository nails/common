<fieldset  id="edit-user-uploads" class="uploads">
	<legend><?=lang( 'accounts_edit_upload_legend' )?></legend>
	<p class="system-alert message no-close">
		<?=lang( 'accounts_edit_upload_message' )?>
	</p>
	<p>
	<?php
	
		foreach ( $user_uploads AS $type => $files ) :
		
			echo '<ul>';
			echo '<li class="type">' . lang( $type ) . '</li>';
			
			if ( $files ) :
			
				foreach ( $files AS $file ) :
				
					$_bucket	= $user_edit->id . '-' . end( explode( '_', $type ) );
					$_ext		= end( explode( '.', $file ) );
					
					echo '<li class="file">';
					
					switch( $_ext ) :
					
						case 'jpg':
						case 'gif':
						case 'png':
						
							echo '<a href="' . cdn_serve( $_bucket, $file ) . '" class="fancybox">';
							echo img( cdn_thumb( $_bucket, $file, 35, 35 ) );
							echo $file;
							echo '</a>';
						
						break;
						
						// --------------------------------------------------------------------------
						
						default :
						
							echo anchor( cdn_serve( $_bucket, $file ), $file );
						
						break;
					
					endswitch;
					
					echo '</li>';
				
				endforeach;
			
			else :
			
				echo '<li class="no-data">' . lang( 'accounts_edit_upload_nofile', lang( $type ) ) . '</li>';
			
			endif;
			
			echo '</ul>';
		
		endforeach;
	
	?>
	</p>
</fieldset>