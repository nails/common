<fieldset id="edit-user-actions">
	<legend><?=lang( 'accounts_edit_actions_legend' )?></legend>
	<p>
	<?php
	
		$return_string = '?return_to=' . urlencode( uri_string() . '?' . $_SERVER['QUERY_STRING'] );
		
		if ( array_search( $user_edit->group_id, array( 0, 1 ) ) === FALSE && $user_edit->group_id != active_user( 'group_id' ) ) :
		
			echo login_as_button( $user_edit->id, $user_edit->password, lang( 'admin_login_as' ) . ' ' . $user_edit->first_name, 'class="awesome" target="_parent"' );
		
		endif;
		
		// --------------------------------------------------------------------------
			
		//	Can't do any of these functions to yourself
		if ( $user_edit->id != active_user( 'id' ) ) :
			
			echo anchor( 'admin/accounts/delete/' . $user_edit->id . '?return_to=' . urlencode( 'admin/accounts' ), lang( 'action_delete' ), 'class="awesome red confirm" data-confirm="' . lang( 'admin_confirm_delete' ) . '"' );
			
		endif;
		
		// --------------------------------------------------------------------------
		
		//	Verified email address?
		if ( $user_edit->is_verified ) :
		
			echo anchor( 'admin/accounts/unverify/' . $user_edit->id . $return_string, lang( 'action_unverify' ), 'class="awesome red"' );
			
		else :
		
			echo anchor( 'admin/accounts/verify/' . $user_edit->id . $return_string, lang( 'action_verify' ), 'class="awesome"' );
			
		endif;
		
		// --------------------------------------------------------------------------
		
		//	Suspended?
		if ( $user_edit->is_suspended ) :
		
			echo anchor( 'admin/accounts/unsuspend/' . $user_edit->id . $return_string, lang( 'action_unsuspend' ), 'class="awesome"' );
			
		else :
		
			echo anchor( 'admin/accounts/suspend/' . $user_edit->id . $return_string, lang( 'action_suspend' ), 'class="awesome red"' );
			
		endif;
							
	?>
	</p>
	
	<!--	CLEARFIX	-->
	<div class="clear"></div>
	
</fieldset>