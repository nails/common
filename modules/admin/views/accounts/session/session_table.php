<!--	CUSTOM JS	-->
<script type="text/javascript">
<!--// Hide from old browsers
	
	$(function() {
	
		$( 'a.fancybox' ).fancybox();
	
	});
	
//-->
</script>

<!--	OVERRIDE STYLES	-->
<style type="text/css">
				
	td.session_id			{ width: 150px; }
	td.ip_address			{ width: 100px; }
	td.user_agent			{ width: 300px; }
	td.last_activity		{ width: 100px; }
	td.user_data			{ width: 60px; text-align: center; }
	td.last_page			{ width: auto; }
	td.user_id				{ width: 50px; text-align: center; }

	td#no_records			{ height: 75px; text-align: center; color: #aaa; text-transform: uppercase; }
			
</style>

<!--	START RENDERING TABLE	-->
<section>		
	<table id="account_list">
	
		<!--	TABLE HEAD	-->
		<thead>
		
			<tr>
				
				<!--	SESSION ID	-->
				<th class="session_id">SESSION ID</th>
				
				<!--	IP ADDRESS	-->
				<th class="last_ip">IP</th>
				
				<!--	USER AGENT	-->
				<th class="user_agent">USER AGENT</th>
				
				<!--	LAST ACTIVITY	-->
				<th class="last_activity">LAST ACTIVITY</th>
				
				<!--	USER DATA	-->
				<th class="user_data">USER DATA</th>
				
				<!--	LAST PAGE	-->
				<th class="last_page">LAST PAGE</th>
				
				<!--	USER ID	-->
				<th class="user_id">USER</th>
				
								
			
			
			</tr>
		
		</thead>
		<!--	/TABLE HEAD-->
		
		
		<!--	LIST SESSIONS	-->
		<tbody>
		
			<?php if ( count( $sessions ) == 0 ) : ?>
			
				<tr>
					<td colspan="9" id="no_records">
					
						<p>No records found</p>
					
					</td>
				</tr>
			
			<?php else : ?>
			
				<?php foreach ( $sessions AS $s ) : ?>
			
				<tr>
				
					<td class="session_id" title="<?=$s->session_id?>"><?=ellipsize( $s->session_id, 15, 1 )?></td>
					<td class="ip_address"><?=$s->ip_address?></td>
					<td class="user_agent" title="<?=$s->user_agent?>">
					<?php
						
						$_ua = $this->agent->from_string( $s->user_agent );
						echo ( $_ua->browser ) ? '<strong>' . $_ua->browser . '</strong> ' : NULL;
						echo ( $_ua->version ) ? 'v' . $_ua->version . ' on ' : NULL;
						echo '<strong>' . $_ua->platform . '</strong>';
					?>
					</td>
					<td class="last_activity"><?=nice_time( (int) $s->last_activity )?></td>
					<td class="user_data">
						
						<a href="#userdata_<?=$s->session_id?>" class="fancybox a-button a-button-small">View Data</a>
						<div style="display:none">
							<div id="userdata_<?=$s->session_id?>" style="padding:20px;">
								<pre><?php
									
									$_ud = unserialize( $s->user_data );
									echo print_r( $_ud, TRUE );
									
								?></pre>
							</div>
						</div>
						
					</td>
					<td class="last_page" title="<?=$s->last_page?>">
						<?=ellipsize( $s->last_page, 25 )?>
					</td>
					<td class="user_id">
					<?php
					
						if ( isset( $_ud['id'] ) ) :
							
							$_user = get_userobject()->get_user( $_ud['id'] );
							
							if ( $_user->profile_img ) :
							
								echo anchor( 'admin/accounts/edit/' . $s->user_id, img( cdn_thumb( 'profile_images', $_user->profile_img, 35, 35 ) ) );
								
							else :
							
								echo anchor( 'admin/accounts/edit/' . $s->user_id, img( cdn_placeholder( 35, 35 ) ) );
							
							endif;
						
						else :
						
							echo '<span style="color:#ccc;">&mdash;</span>';
						
						endif;
					
					?>
					</td>

				
				</tr>
				
				<?php endforeach; ?>
				
			<?php endif; ?>
		
		</tbody>
		<!--	/LIST SESSIONS	-->
	
	</table>
	
</section>
	
<div class="clear"></div>