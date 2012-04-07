<div class="box specific" id="box_edit_profile_img" style="width:280px;">

	<h2>
		Profile Image
		<a href="#" class="toggle">close</a>
	</h2>
	
	<div class="container" style="padding:0 12px;text-align:center;">

		<?php if ( empty( $user_edit->profile_img ) || ! file_exists( CDN_PATH . 'profile_images/' . $user_edit->profile_img ) ) : ?>
		
			<?=img( array( 'src' => cdn_placeholder( 240, 240, 1 ), 'id' => 'preview_image' ) )?>
			
		<?php else : ?>
		
			<?=anchor( CDN_SERVER . 'profile_images/' . $user_edit->profile_img, img( array( 'src' => cdn_thumb( 'profile_images', $user_edit->profile_img, 240, 240 ), 'id' => 'preview_image' ) ), 'class="fancybox"' )?>
			<br>
			<a href="<?=site_url( 'admin/accounts/delete_profile_img/' .$user_edit->id )?>" class="a-button a-button-small a-button-red" style="margin-left:2px;">Remove image</a>
			
		<?php endif; ?>
	
	</div>

</div>