<div class="fieldset">
	<div class="legend">Actions</div>
	<p>
	<?php
	
		$return_string = '?return_to=' . urlencode( uri_string() . '?' . $_SERVER['QUERY_STRING'] );
		
		if ( array_search( $user_edit->group_id, array( 0, 1 ) ) === FALSE && $user_edit->group_id != active_user( 'group_id' ) ) :
		
			echo login_as_button( $user_edit->id, $user_edit->password, 'Login As ' . $user_edit->first_name, 'class="awesome small left"' );
			
		endif;
		
		// --------------------------------------------------------------------------
			
		//	Can't do any of these functions to yourself
		if ( $user_edit->id != active_user( 'id' ) ) :
		
			if ( ! $user_edit->active ) :
			
				echo anchor( 'admin/accounts/activate/' . $user_edit->id . $return_string, 'Activate', 'class="awesome small green left"' );
				
			endif;
			
			if ( $user_edit->active == 2 ) :
			
				echo anchor( 'admin/accounts/unban/' . $user_edit->id . $return_string, 'Unban', 'class="awesome small right"' );
				
			else :
			
				echo anchor( 'admin/accounts/ban/' . $user_edit->id . $return_string, 'Ban', 'class="awesome red small right"' );
				
			endif;
		
		endif;
							
	?>
	</p>
	
	<!--	CLEARFIX	-->
	<div class="clear"></div>
	
</div>