<fieldset>
	<legend>Actions</legend>
	<p>
	<?php
	
		$return_string = '?return_to=' . urlencode( uri_string() . '?' . $_SERVER['QUERY_STRING'] );
		
		if ( array_search( $user_edit->group_id, array( 0, 1 ) ) === FALSE && $user_edit->group_id != active_user( 'group_id' ) ) :
		
			echo login_as_button( $user_edit->id, $user_edit->password, 'Login As ' . $user_edit->first_name, 'class="awesome small left"' );
		
		else :
		
			$_no_login = TRUE;
		
		endif;
		
		// --------------------------------------------------------------------------
			
		//	Can't do any of these functions to yourself
		if ( $user_edit->id != active_user( 'id' ) ) :
			
			echo anchor( 'admin/accounts/delete/' . $user_edit->id . '?return_to=' . urlencode( 'admin/accounts' ), 'Delete', 'class="awesome red small right confirm" data-confirm="CONFIRM DELETE\n\nThis action is not undoable, are you sure?"' );
			
			if ( $user_edit->active == 2 ) :
			
				echo anchor( 'admin/accounts/unsuspend/' . $user_edit->id . $return_string, 'Unsuspend', 'class="awesome small right"' );
				
			else :
			
				echo anchor( 'admin/accounts/suspend/' . $user_edit->id . $return_string, 'Suspend', 'class="awesome red small right"' );
				
			endif;
					
		elseif ( isset( $_no_login ) ) :
		
			echo '<p style="text-align:center;color:#aaa;">Sorry, there are no actions you can currently perform upon this user.</p>';
		
		endif;
							
	?>
	</p>
	
	<!--	CLEARFIX	-->
	<div class="clear"></div>
	
</fieldset>