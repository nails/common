<?php

	//	Define method so links are correct
	$method = $this->uri->segment( 3, 'index' );

?>
<!--	CUSTOM JS	-->
<script type="text/javascript">
<!--// Hide from old browsers
	
	$(function() {
	
		//	Generic Fancybox
		$( 'a.fancybox' ).fancybox();
		
		//	CV Fancys
		$( 'a.fancybox-cv' ).fancybox({
		'padding': 10,
		'type':'iframe',
		'width': 870,
		'height': $(window).height(),
		'centerOnScroll': false,
		'overlayOpacity': 0.6,
		'overlayColor': '#000'
		});
		
		$( 'a.order_details' ).fancybox({
		'padding': 10,
		'type':'iframe',
		'width': 800,
		'height': $(window).height(),
		'centerOnScroll': false,
		'overlayOpacity': 0.6,
		'overlayColor': '#000'
		});
	
	});
	
//-->
</script>

<!--	OVERRIDE STYLES	-->
<style type="text/css">
				
	th.first, td.first		{ width: 125px }
	th.last, td.last		{ width: 125px }
	th.email, td.email		{ width: auto }
	th.group,td.group		{ width: 70px }
	th.options, td.options	{ width: 100px; }
	td.profile_img			{ width:40px; text-align: center; }
	td.cv					{ width:40px; text-align: center; color: #e7e7e7; }
	td.percentage			{ width:20px; text-align: center; }
	td.score				{ width:20px; text-align: center; }
	td.options				{ text-align: center; }
	td.options img			{ box-shadow: none; -moz-box-shadow: none; -webkit-box-shadow: none; vertical-align: middle; }
	td.options span			{ color: #ccc; }
	td 						{ text-overflow: ellipsis; white-space: nowrap; overflow: hidden; }
	td#no_records			{ height: 75px; text-align: center; color: #aaa; text-transform: uppercase; }
			
</style>

<!--	START RENDERING TABLE	-->
<section>		
	<table id="account_list">
	
		<!--	TABLE HEAD	-->
		<thead>
			<tr>
				<th class="img_etc" colspan="4">&nbsp;</th>
				<th class="first">First Name</th>
				<th class="last">Surname</th>
				<th class="email">Email</th>
				<th class="group">Group</th>
				<th class="options">Status</th>
			</tr>
		</thead>
		<!--	/TABLE HEAD-->
		
		
		<!--	LIST USERS	-->
		<tbody>
		
			<?php if ( count( $users ) == 0 ) : ?>
			
				<tr>
					<td colspan="9" id="no_records">
					
						<p>No records found</p>
					
					</td>
				</tr>
			
			<?php else : ?>
			
				<?php foreach ( $users AS $u ) : ?>
			
				<tr>
				
					<td class="profile_img">
					<?php
					
						if ( ! empty( $u->profile_img ) ) :
						
							echo anchor( CDN_SERVER . 'profile_images/' . $u->profile_img, img( cdn_thumb( 'profile_images', $u->profile_img, 35, 35 ) ), 'class="fancybox"' );
						
						else :
						
							echo img( cdn_placeholder( 35, 35, 1 ) );
						
						endif;
					
					?>
					</td>
					<td class="cv">
						<?=( $u->group_id == 0 || $u->group_id == 1 || $u->group_id == 2 ) ? anchor( 'intern/account/profile_preview/'.$u->id , 'CV', 'class="fancybox-cv a-button a-button-small"') : '&mdash;'?>
					</td>
					<td class="percentage">
						<?=$u->percent_complete?>%
					</td>
					<td class="score"><?=number_format( $u->profile_score )?></td>
					<td class="first"><?=( empty( $u->first_name ) )	? '<span style="color:#ccc;"> &nbsp;&mdash;</span>' : title_case( $u->first_name )?></td>
					<td class="last"><?=( empty( $u->last_name ) )	? '<span style="color:#ccc;"> &nbsp;&mdash;</span>' : title_case( $u->last_name )?></td>
					<td class="email"><?=safe_mailto( $u->email )?></td>
					<td class="group"><?=title_case( $u->group_name )?></td>
					<td class="options">
						
						<?=( $u->opt_in ) ? img( array( 'src' => 'assets/app/img/icons/tick.png', 'rel' => 'tooltip', 'title' => 'Manually confirmed!' ) ) : NULL ?>
						<?=( $u->shortlisted ) ? img( array( 'src' => 'assets/app/img/icons/search.png', 'rel' => 'tooltip', 'title' => 'Shortlisted!' ) ) : NULL ?>
						<?=( $u->selected ) ? img( array( 'src' => 'assets/app/img/icons/money.png', 'rel' => 'tooltip', 'title' => 'Purchased!' ) ) : NULL ?>
						<?=( $u->employed ) ? img( array( 'src' => 'assets/app/img/icons/person.png', 'rel' => 'tooltip', 'title' => 'Employed!' ) ) : NULL ?>
						
						<?=( $u->response == 1 ) ? img( array( 'src' => 'assets/app/img/icons/tick.png', 'rel' => 'tooltip', 'title' => 'Employed!' ) ) : NULL ?>

					</td>
				
				</tr>
				
				<?php endforeach; ?>
				
			<?php endif; ?>
		
		</tbody>
		<!--	/LIST USERS	-->
	
	</table>
	
</section>