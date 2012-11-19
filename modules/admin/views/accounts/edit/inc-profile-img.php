<div class="fieldset">
	<div class="legend">Profile Image</div>
	<?php
	
		if ( empty( $user_edit->profile_img ) ) :
	
			echo img( array( 'src' => cdn_placeholder( 100, 125, 1 ), 'id' => 'preview_image', 'class' => 'left', 'style' => 'margin-right:10px;' ) );
			echo form_upload( 'profile_img' );
		
		else :

			$_img = array(
				'src'	=> cdn_thumb( 'profile-images', $user_edit->profile_img, 100, 125 ),
				'id'	=> 'preview_image',
				'style'	=> 'border:1px solid #CCC;padding:0;margin-right:10px;'
			);
			
			echo anchor( cdn_serve( 'profile-images', $user_edit->profile_img ), img( $_img ), 'class="fancybox left"' );
			echo '<p>';
			echo form_upload( 'profile_img' );
			echo '<br />';
			echo anchor( '#', 'Remove Image', 'class="awesome small red" style="margin-top:10px;"' );
			echo '</p>';
	
		endif;
	?>
	
	<!--	CLEARFIX	-->
	<div class="clear"></div>
</div>