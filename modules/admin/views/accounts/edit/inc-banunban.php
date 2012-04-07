<div class="box specific" id="box_edit_banunban" style="width:280px;">

	<h2>
		Actions: Ban / Unban
		<a href="#" class="toggle">close</a>
	</h2>
	
	<div class="container" style="padding:0 12px;text-align:center;">
	
		<p><strong>Activate, Ban or Unban <?=$user_edit->first_name?></strong></p>
		
		<p style="padding-bottom:10px;">
		<?php
		
			//	Can't do any of these functions to yourself
			if ( $user_edit->id != active_user( 'id' ) ) :
			
				//echo anchor( 'admin/accounts/delete/' . $user_edit->user_id . $return_string, 'Delete', 'class="a-button a-button-small a-button-red"' );
			
				if( ! $user_edit->active )
					echo anchor( 'admin/accounts/activate/' . $user_edit->id . $return_string, 'Activate', 'class="a-button a-button-small a-button-green"' );
				
				if( $user_edit->active == 2 ) :
				
					echo anchor( 'admin/accounts/unban/' . $user_edit->id . $return_string, 'Unban', 'class="a-button a-button-small"' );
					
				else :
				
					echo anchor( 'admin/accounts/ban/' . $user_edit->id . $return_string, 'Ban', 'class="a-button a-button-small a-button-red"' );
					
				endif;
			
			else :
			
				echo 'You cannot perform these actions on ' . $user_edit->first_name . '.';
			
			endif;
		
		?>
		</p>
	</div>

</div>