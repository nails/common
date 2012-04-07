<!--	CUSTOM CSS	-->
<style type="text/css">
				
	th.first, td.first		{ width: 125px }
	th.last, td.last		{ width: 125px }
	th.email, td.email		{ width: auto }
	th.options, td.options	{ width: 100px; text-align: center; }
	td.profile_img			{ width:40px; text-align: center; }
	td.cv					{ width:40px; text-align: center; color: #e7e7e7; }
	td.percentage			{ width:20px; text-align: center; }
	td.score				{ width:20px; text-align: center; }
	td 						{ text-overflow: ellipsis; white-space: nowrap; overflow: hidden; }
	td#no_records			{ height: 75px; text-align: center; color: #aaa; text-transform: uppercase; }
			
</style>

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
		'width': 800,
		'height': $(window).height(),
		'centerOnScroll': false,
		'overlayOpacity': 0.6,
		'overlayColor': '#000'
		});
		
		//	Row highlighter
		$( 'input[type=checkbox]' ).click( function() {
		
			if ( $(this).attr( 'checked' ) ) {
			
				$( this ).parents( 'tr' ).css( 'background', '#00B3DF' );
			
			} else {
			
				$( this ).parents( 'tr' ).css( 'background', '' );
			
			}
		
		});
		
		
		//	Form submit check
		$( 'form' ).submit( function() {
		
			if ( $( 'input[type=checkbox]:checked' ).length == 0 ) {
			
				alert( 'You must choose at least one candidate to shortlist.' );
				return false;
			
			} else {
			
				return confirm( 'You are about to send all the selected candidates to the employer who will then be able browse the anonymous profiles.\n\nContinue?' );
			
			}
		
		});
	
	});
	
//-->
</script>

<!--	PAGE TITLE	-->
<section>
	<h1>Orders &rsaquo; Complete Order (<?=$order->ref?>)</h1>	
</section>

<p>
	Below are the top candidates (max 100) as matched by the intern matching engine. Please select which candidates you would like to put forward to the employer (at least one candidate must be selected).
</p>








<!--	OPEN FORM	-->
<?=form_open( 'admin/orders/complete_order/' . $this->uri->segment( 4 ) . '?return_to=' . $this->input->get( 'return_to' ) )?>
<?=form_hidden( 'process', TRUE )?>




<!--	START RENDERING TABLE	-->
<section>		
	<table id="account_list">
	
		<!--	TABLE HEAD	-->
		<thead>
		
			<tr>
				
				<th>Rank</th>
				
				<!--	PROFILE IMG, CV, % COMPLETE	& SCORE	-->
				<th colspan="4">&nbsp;</th>
				
								
				<!--	FIRST_NAME	-->
				<th class="first">First Name</th>
				
				
				<!--	LAST_NAME	-->
				<th class="last">Surname</th>
				
				
				<!--	EMAIL	-->
				<th class="email">Email</th>
				
				
				<!--	Confirmed?	-->
				<th>Confirmed?</th>
				
				
				<!--	OPTIONS	-->
				<th class="options">Shortlist</th>
			
			</tr>
		
		</thead>
		<!--	/TABLE HEAD-->
		
		
		<!--	LIST USERS	-->
		<tbody>
		
			<?php if ( count( $order->matches ) == 0 ) : ?>
			
				<tr>
					<td colspan="10" id="no_records">
					
						<p>No records found</p>
						<p><small>This shouldn't ever happen in a real life order. If it did something is wrong. Let the lads know.</small></p>
					
					</td>
				</tr>
			
			<?php else : ?>
			
				<?php $count = 1; ?>
				
				<?php foreach ( $order->matches AS $u ) : ?>
					
				<tr>
				
					<td>
						<?=$count?>
					</td>
				
					<td class="profile_img">
					<?php
						if ( ! empty( $u->profile_img ) ) :
						
							echo anchor( CDN_SERVER . 'profile_images/'.$u->profile_img, img( cdn_thumb( 'profile_images', $u->profile_img, 35, 35 ) ), 'class="fancybox"' );
						
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
					<td style="text-align:center;">
					<?php
						if( $u->response == 1 ) :
							echo img( array( 'src' => 'assets/app/img/icons/tick.png', 'style' => 'box-shadow:none' ) );
							echo '<br>Confirmed!';
						else:
							echo img( array( 'src' => 'assets/app/img/icons/pending.png', 'style' => 'box-shadow:none' ) );
							echo '<br>No response';
						endif;
					?>
					</td>
					<td class="options">
						
						<?=form_checkbox( 'chosen_one[]', $u->id )?>
						
					</td>
				
				</tr>
				
				<?php $count = $count + 1; ?>
				
				<?php endforeach; ?>
				
			<?php endif; ?>
		
		</tbody>
		<!--	/LIST USERS	-->
	
	</table>
	
	<?php if ( count( $order->matches ) != 0 ) : ?>
	<p style="margin-top:20px;">
		<?=form_submit( 'submit', 'Send Selected Interns to Employer', '' )?>
	</p>
	<?php endif; ?>
	
</section>

</form>

<div class="clear"></div>