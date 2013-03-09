<fieldset>
	<legend><?=lang( 'accounts_edit_social_legend' )?></legend>
	<?php
	
		if ( $user_edit->fb_id  || $user_edit->tw_id || $user_edit->li_id ) :
		
			if ( $user_edit->fb_id ) :
			
				echo '<p>' . lang( 'accounts_edit_social_connected', 'Facebook' ) . '</p>';
			
			endif;
			
			// --------------------------------------------------------------------------
			
			if ( $user_edit->tw_id ) :
			
				echo '<p>' . lang( 'accounts_edit_social_connected', 'Twitter' ) . '</p>';
			
			endif;
			
			// --------------------------------------------------------------------------
			
			if ( $user_edit->li_id ) :
			
				echo '<p>' . lang( 'accounts_edit_social_connected', 'LinkedIn' ) . '</p>';
			
			endif;
			
			// --------------------------------------------------------------------------
		
		else :
		
			echo '<p>' . lang( 'accounts_edit_social_none' ) . '</p>';
		
		endif;
	
	?>
</fieldset>