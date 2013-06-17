<div class="group-stats browse">

	<p>
		Whenever users interact with the site 'events' are created.
	</p>
	
	<?php
	
		$this->load->view( 'admin/stats/utilities/search' );
		$this->load->view( 'admin/stats/utilities/pagination' );
	
	?>
	
	<table>
		<thead>
			<tr>
				<th class="created">Date</th>
				<th class="user">User</th>
				<th class="event">Type of Event</th>
			</tr>
		</thead>
		<tbody>
		<?php
		
			if ( $events->data ) :
			
				foreach ( $events->data AS $event ) :

					echo '<tr class="event">';

					echo '<td class="created"><span class="nice-time">' . $event->created . '</span></td>';
					
					// --------------------------------------------------------------------------

					echo '<td class="user">';

					if ( $event->user->id ) :

						if ( $event->user->profile_img ) :

							echo anchor( cdn_serve( 'profile-images', $event->user->profile_img ), img( cdn_thumb( 'profile-images', $event->user->profile_img, 30, 30 ) ), 'class="fancybox"' );

						else :

							echo img( cdn_blank_avatar( 30, 30, $event->user->gender ) );

						endif;
						echo anchor( 'admin/accounts/edit/' . $event->user->id, $event->user->first_name . ' ' . $event->user->last_name, 'class="fancybox" data-fancybox-type="iframe"' );
						echo '<small>' . mailto( $event->user->email ) . '</small>';

					else :

						echo '<span class="no-data">Unknown User</span>';

					endif;
					echo '</td>';
					
					// --------------------------------------------------------------------------

					echo '<td class="event">';
					echo $event->type->label ? $event->type->label : title_case( str_replace( '_', ' ', $event->type->slug ) );
					echo '<small>' . $event->type->description . '</small>';
					echo '</td>';
					
					echo '</tr>';
				
				endforeach;
			
			else :
			
				echo '<tr>';
				echo '<td colspan="5" class="no-data">';
				echo 'No Events found';
				echo '</td>';
				echo '</tr>';
			
			endif;
		
		?>
		</tbody>
	</table>

	<?php
	
		$this->load->view( 'admin/stats/utilities/pagination' );
	
	?>
</div>