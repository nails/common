<div class="box specific" id="box_edit_login_as" style="width:280px;">

	<h2>
		Actions: Login As
		<a href="#" class="toggle">close</a>
	</h2>
	
	<div class="container" style="padding:0 12px;text-align:center;">


		<?php
		
			$return_string = '?return_to=' . urlencode( uri_string() . '?' . $_SERVER['QUERY_STRING'] );
			
			if ( array_search( $user_edit->group_id, array( 0, 1 ) ) === FALSE && $user_edit->group_id != active_user( 'group_id' ) ) :
			
		?>
		
			<p>Login as <?=$user_edit->first_name?> to update any of their details</p>
			<p><?=login_as_button( $user_edit->id, $user_edit->password, 'Login As', 'class="a-button a-button-blue"' )?></p>
			
		<?php
		
			else :
			
				echo '<p style="padding-bottom:10px;">You cannot sign in as ' . $user_edit->first_name . '.</p>';
				
			endif;
								
		?>
	
	</div>

</div>