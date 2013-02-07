<tr>
	<td class="id"><?=number_format( $member->id )?></td>
	<td class="details">
		<?php
		
			if ( $member->profile_img ) :
			
				echo anchor( cdn_serve( 'profile-images', $member->profile_img ), img( array( 'src' => cdn_thumb( 'profile-images', $member->profile_img, 65, 65 ), 'class' => 'profile-img' ) ), 'class="fancybox"' );
			
			else :
			
				switch( $member->gender ) :
				
					case 'female' :	echo img( array( 'src' => cdn_blank_avatar( 65, 65, 'female' ), 'class' => 'profile-img' ) );	break;
					default	: 		echo img( array( 'src' => cdn_blank_avatar( 65, 65, 'male' ), 'class' => 'profile-img' ) );		break;
				
				endswitch;
			
			endif;
			
			echo '<div>';
			
			switch ( $this->input->get( 'sort' ) ) :
			
				case 'um.last_name' :	echo '<strong>' . $member->last_name . ', ' . $member->first_name . '</strong>';	break;
				default :				echo '<strong>' . $member->first_name . ' ' . $member->last_name . '</strong>';		break;
			
			endswitch;
			
			echo '<small>';
			echo isset( $member->telephone ) && $member->telephone ? $member->telephone . ' | ' : '';
			echo $member->email;
			echo $member->active ? img( array( 'src' => NAILS_URL . '/img/admin/icons/verified-email.png', 'class' => 'verified', 'rel' => 'tooltip', 'title' => 'Verified Email Address' ) ) : '';
			echo $member->fb_id ? img( array( 'src' => NAILS_URL . '/img/admin/icons/verified-facebook.png', 'class' => 'verified', 'rel' => 'tooltip', 'title' => 'Connected to Facebook' ) ) : '';
			echo $member->linkedin_id ? img( array( 'src' => NAILS_URL . '/img/admin/icons/verified-linkedin.png', 'class' => 'verified', 'rel' => 'tooltip', 'title' => 'Connected to LinkedIn' ) ) : '';
			echo '</small>';
			
			if ( $member->last_login ) :
			
				echo '<small>Last login: <span class="nice-time">' . date( 'Y-m-d H:i:s', $member->last_login ) . '</span> (' . $member->login_count . ' logins)</small>';
			
			else :
			
				echo '<small>Last login: Never Logged In</small>';
			
			endif;
			echo '</div>';
		
		?>
	</td>
	<td class="group"><?=$member->group_name?></td>
	
	<!--	EXTRA COLUMNS	-->
	<?php
	
		foreach ( $columns AS $col ) :
		
			$this->load->view( 'admin/accounts/utilities/user_row_column_' . $col['view'] );
			
		endforeach;
	
	?>
	
	<!--	ACTIONS	-->
	<td class="actions">
		<?php
		
			$_return = $_SERVER['QUERY_STRING'] ? uri_string() . '?' . $_SERVER['QUERY_STRING'] : uri_string();
			$_return = '?return_to=' . urlencode( $_return );
			
			//	These buttons are always available
			echo anchor( login_as_url( $member->id, $member->password ), 'Login As', 'class="awesome small grey"' );
			echo anchor( 'admin/accounts/edit/' . $member->id . $_return, 'Edit', 'data-fancybox-type="iframe" data-fancybox-group="interns" class="edit fancybox-max awesome small grey"' );
			
			// --------------------------------------------------------------------------
			
			//	These buttons are dynamic (based on user's state and admin's permissions)
			if ( $member->active == 2 ) :
			
				echo anchor( 'admin/accounts/unsuspend/' . $member->id . $_return, 'Unsuspend', 'class="awesome small green"' );
			
			else :
			
				echo anchor( 'admin/accounts/suspend/' . $member->id . $_return, 'Suspend', 'class="awesome small red"' );
			
			endif;
			
			if ( $member->active == 3 ) :
			
				echo anchor( 'admin/accounts/activate/' . $member->id . $_return, 'Activate', 'class="awesome small green"' );
			
			else :
			
				echo anchor( 'admin/accounts/deactivate/' . $member->id . $_return, 'Deactivate', 'class="awesome small red"' );
			
			endif;
			
			if ( $user->has_permission( 'admin.accounts.delete' ) && $member->id != active_user( 'id' ) ) :
			
				echo anchor( 'admin/accounts/delete/' . $member->id . $_return, 'Delete', 'class="awesome small red"' );
			
			endif;
			
			// --------------------------------------------------------------------------
			
			//	These buttons are variable between views
			foreach ( $actions AS $button ) :
			
				echo anchor( $button['url'] . $_return, $button['label'], 'class="awesome small ' . $button['class'] . '"' );
				
			endforeach;
		
		?>
	</td>
</tr>