<div class="box specific"  id="box_employer_edit_admins">

	<h2>
		Employer Administrators
		<a href="#" class="toggle">close</a>
	</h2>

	<div class="container" style="padding:3px 12px; 2px;">
	
		<?php 
				
				$return_string = '?return_to=' . urlencode( uri_string() . '?' . $_SERVER['QUERY_STRING'] );
				
				if ( $employer->admins ) :
				
					echo '<ul>';
					
					foreach ( $employer->admins AS $s ) : 
					
					?>
					
						<li>
							<a href="<?=site_url( 'admin/accounts/edit/' . $s->id )?>" style="padding-bottom:5px;;display:block;">
							
								<?php if ( $s->profile_img ) : ?>
								
									<img src="<?=cdn_thumb( 'profile_images', $s->profile_img, 52, 52 )?>" style="float:left;margin-right:5px;" />
								
								<?php else : ?>
								
									<img src="<?=cdn_placeholder( 52, 52, 1 )?>" style="float:left;margin-right:5px;" />
								
								<?php endif; ?>
								
								
								<?=$s->first_name . ' ' . $s->last_name?>
								
								<?php if ( $s->group_id == 3 ) : ?>
								
									<span style="font-weight:bold;color:red;font-size:0.8em;padding-left:5px;">MANAGER</span>
									
								<?php endif; ?>
								
							</a>
							
							<?=login_as_button( $s->user_id, $s->password )?>
							
							<!--	CLEARFIX	-->
							<span class="clearfix"></span>
							
						</li>
						
					<?php
					
					endforeach;
					
					echo '</ul>';
					
				endif;
		
		?>
	
	
	</div>

</div>	