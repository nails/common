<fieldset>
	<legend>Profile Image</legend>
	<?php
	
		$_error = isset( $upload_error ) ? 'error' : NULL;
		
		echo '<div class="field ' . $_error . '">';
		
		if ( empty( $user_edit->profile_img ) ) :
	
			echo img( array( 'src' => cdn_blank_avatar( 100, 125 ), 'id' => 'preview_image', 'class' => 'left', 'style' => 'margin-right:10px;' ) );
			echo form_upload( 'profile_img' );
		
		else :

			$_img = array(
				'src'	=> cdn_thumb( 'profile-images', $user_edit->profile_img, 100, 125 ),
				'id'	=> 'preview_image',
				'style'	=> 'border:1px solid #CCC;padding:0;margin-right:10px;'
			);
			
			echo anchor( cdn_serve( 'profile-images', $user_edit->profile_img ), img( $_img ), 'class="fancybox left"' );
			echo '<p>';
			echo form_upload( 'profile_img', NULL, 'style="float:none;"' ) . '<br />';
			$_return = '?return_to=' . urlencode( uri_string() . '?' . $_SERVER['QUERY_STRING'] );
			echo anchor( 'admin/accounts/delete_profile_img/' . $user_edit->id . $_return, 'Remove Image', 'class="awesome small red" style="margin-top:10px;"' );
			echo '</p>';
	
		endif;
		
		if ( $_error ) :
		
			echo '<span class="error">';
			
			foreach ( $upload_error AS $err ) :
			
				echo $err . '<br />';
			
			endforeach;
			
			echo '</span>';
		
		endif;
		
		echo '<div class="clear"></div>';
		echo '</div>';
	?>
	
	<!--	CLEARFIX	-->
	<div class="clear"></div>
</fieldset>