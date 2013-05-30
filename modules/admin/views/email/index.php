<div class="group-email archive">
	<p>
		<?=lang( 'email_index_intro' )?>
	</p>
	
	<hr />
	
	<?php
	
		//	TODO: Add search facilities
		
		$this->load->view( 'admin/email/utilities/pagination' );
	
	?>
	
	<table>
		<thead>
			<tr>
				<th class="id"><?=lang( 'email_index_thead_id' )?></th>
				<th class="ref"><?=lang( 'email_index_thead_ref' )?></th>
				<th class="to"><?=lang( 'email_index_thead_to' )?></th>
				<th class="queued"><?=lang( 'email_index_thead_queued' )?></th>
				<th class="sent"><?=lang( 'email_index_thead_sent' )?></th>
				<th class="type"><?=lang( 'email_index_thead_type' )?></th>
				<th class="reads"><?=lang( 'email_index_thead_reads' )?></th>
				<th class="clicks"><?=lang( 'email_index_thead_clicks' )?></th>
				<th class="actions"><?=lang( 'email_index_thead_actions' )?></th>
			</tr>
		</thead>
		<tbody>
			<?php
			
				if ( $emails->data ) :
				
					foreach ( $emails->data AS $email ) :
					
						?>
						<tr>
							<td class="id"><?=number_format( $email->id )?></td>
							<td class="ref"><?=$email->ref?></td>
							<td class="to">
								<?php
								
									if ( $email->send_to ) :
									
										if ( $email->profile_img ) :
										
											echo img( cdn_thumb( 'profile-images', $email->profile_img, 45, 45 ) );
										
										else :
										
											switch ( $email->gender ) :
											
												case 'female' :
												
													echo img( cdn_blank_avatar( 45, 45, 'female' ) );
												
												break;
												
												case 'male' :
												default : 
												
													echo img( cdn_blank_avatar( 45, 45, 'male' ) );
												
												break;
											
											endswitch;
										
										endif;
										
										echo $email->first_name . ' ' . $email->last_name;
										echo '<small>' . mailto( $email->send_to ) . '</small>';

										$_email = $email->send_to;
										
									else :
									
										echo img( cdn_blank_avatar( 45, 45, 'male' ) );
										echo '&mdash;';
										echo '<small>' . mailto( $email->user_email ) . '</small>';

										$_email = $email->user_email;
									
									endif;
								
								?>
							</td>
							<td class="queued">
								<?php
								
									echo '<span class="nice-time">' . $email->time_queued . '</span>';
									echo '<small>' . $email->time_queued . '</small>';
								
								?>
							</td>
							<td class="sent">
								<?php
								
									if ( ! (int) $email->time_sent ) :
									
										echo '<span class="queued">' . lang( 'email_index_queued' ) . '</span>';
									
									else :
									
										echo '<span class="nice-time">' . $email->time_sent . '</span>';
										echo '<small>' . $email->time_sent . '</small>';
										
									endif;
								?>
							</td>
							<td class="type">
								<?=$email->name?>
								<small><?=lang( 'email_index_subject', $email->subject )?></small>
							</td>
							<td class="reads"><?=$email->read_count?></td>
							<td class="clicks"><?=$email->link_click_count?></td>
							<td class="actions">
								<?=anchor( 'email/view_online/' . $email->ref, lang( 'action_preview' ), 'class="awesome small fancybox fancybox.iframe" target="_blank"' )?>
							</td>
						</tr>
						<?php
					
					endforeach;
					
				else :
				
					?>
					<tr>
						<td class="no-data" colspan="9"><?=lang( 'email_index_noemail' )?></td>
					</tr>
					<?php
				
				endif;
				
			?>
		</tbody>
	</table>
	
	<?php
	
		$this->load->view( 'admin/email/utilities/pagination' );
	
	?>
</div>