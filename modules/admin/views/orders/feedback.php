<?php

	$stars = range(0,9);

?><!--	CUSTOM STYLES	-->
<style type="text/css">
	
	body
	{
		background:#FFF;
		margin:0;
		padding:0;
	}
	#tabs
	{
		width:800px;
		margin:0;
		padding:0;
	}
	#tabs li
	{
		display:block;
		clear:none;
		float:left;
		width:398px;
		text-align:center;
		border:1px solid #000;
		border-bottom:1px solid #777;
		border-top-right-radius:4px;
		border-top-left-radius:4px;
		color:#fff;
		margin:0;
		background: #FFF;
		color:#000;
	}
	#tabs li.active
	{
		color:#FFF;
		background:#000;
		border:1px solid #000;
	}
	#tabs li a
	{
		color:inherit;
		text-transform:uppercase;
		padding:5px;
		width:389px;
		display:block;
		font-size:11px;
		text-decoration:none;
		font-weight:bold;
	}
	
	.main
	{
		clear:both;
		width:778px;
		padding:10px;
		border:1px solid #000;
		height:490px;
		overflow-x:hidden;
		overflow-y:scroll;
	}
	
	h2
	{
		text-transform:uppercase;
		font-weight:bold;
		font-size:1.2em;
		padding-bottom:10px;
		margin-bottom:10px;
		border-bottom:1px dotted #ccc;
	}
	
	td.label
	{
		width:150px;
		font-weight:bold;
		vertical-align:top;
	}
	td.label span
	{
		font-weight:normal;
		font-style:italic;
		display:block;
		font-size:0.9em;
	}
	
	.candidate_feedback
	{
		margin: 0;
		padding:0;
		clear:left;
	}
	
	.candidate_feedback span
	{
		display:block;
		float:left;
		width:110px;
		margin-right:5px;
	}
	
	.candidate_feedback em
	{
		font-style:italic;
	}
</style>


<!--	CUSTOM JS	-->
<script type="text/javascript">
<!--//
	
	$( function() {
	
		$( '#tabs a' ).click( function() {
		
			if ( $( this ).parent().attr( 'id' ) == 'tab-employer' ) {
			
				//	Content
				$( '#content-employer' ).show();
				$( '#content-intern' ).hide();
				
				//	Tabs
				$( '#tab-employer' ).addClass( 'active' );
				$( '#tab-intern' ).removeClass( 'active' );
			
			} else {
			
				//	Content
				$( '#content-employer' ).hide();
				$( '#content-intern' ).show();
				
				//	Tabs
				$( '#tab-employer' ).removeClass( 'active' );
				$( '#tab-intern' ).addClass( 'active' );
			
			}
			
			return false;
		
		});
		
		$( '.stars' ).stars( { 'inputType' : 'select', 'cancelShow' : false } );
		
		
		$( 'a#employer_stage_one_table_toggle' ).click( function() {
		
			$( '#employer_stage_one_table' ).slideToggle();
			
			if ( $( '#employer_stage_one_table' ).height() != 1 ) {
			
				$( this ).html( 'Show Feedback' );
				
			} else {
			
				$( this ).html( 'Hide Feedback' );
			
			}
			
			return false;
		
		});
		
		$( 'a#employer_stage_two_table_toggle' ).click( function() {
		
			$( '#employer_stage_two_table' ).slideToggle();
			
			if ( $( '#employer_stage_two_table' ).height() != 1 ) {
			
				$( this ).html( 'Show Feedback' );
				
			} else {
			
				$( this ).html( 'Hide Feedback' );
			
			}
			
			return false;
		
		});
		
		$( 'a#intern_stage_two_table_toggle' ).click( function() {
		
			$( '#intern_stage_two_table' ).slideToggle();
			
			if ( $( '#intern_stage_two_table' ).height() != 1 ) {
			
				$( this ).html( 'Show Feedback' );
				
			} else {
			
				$( this ).html( 'Hide Feedback' );
			
			}
			
			return false;
		
		});
	
	});
//-->
</script>

<!--	TABS	-->
<ul id="tabs">
	<li class="active" id="tab-employer">
		<a href="#" >Employer Feedback</a>
	</li>
	<li id="tab-intern">
		<a href="#">Intern Feedback</a>
	</li>
</ul>


<!--	EMPLOYER FEEDBACK	-->
<div id="content-employer" class="main" style="display:block;">

	<h2>
		Stage One Feedback
		<?php if ( empty( $feedback['employer']['stage_1']['main'] ) ) : ?>

			<span class="right" style="font-size:0.7em;">No feedback yet submitted</span>

		<?php else : ?>
		
			<a href="#" id="employer_stage_one_table_toggle" class="showhide a-button a-button-small right">Show Feedback</a>
		
		<?php endif; ?>
	</h2>
	<?php if ( ! empty( $feedback['employer']['stage_1']['main'] ) ) : ?>
	
		<div id="employer_stage_one_table" style="display:none;">
		<table>
		
			<tr>
			
				<td class="label">Feedback submitted by:</td>
				<td>
					<?=$feedback['employer']['stage_1']['main']->first_name . ' ' . $feedback['employer']['stage_1']['main']->last_name?>
					- <?=nice_time( $feedback['employer']['stage_1']['main']->created )?>
				</td>
			</tr>
			<tr>
				<td class="label">Employed IA Candidate:</td>
				<td><?=( $feedback['employer']['stage_1']['main']->employed == 1 )? 'Yes' : 'No' ?></td>
			</tr>
				
			<?php if ( $feedback['employer']['stage_1']['main']->employed != 1 ) : ?>
			
			<tr>
				<td class="label">Did not employ because:</td>
				<td><?php
				
					switch( $feedback['employer']['stage_1']['main']->no_order_reason ) :
					
						case 1	: echo 'reason 1 - to be confirmed';		break;
						case 2	: echo 'reason 2 - to be confirmed';		break;
						default	: echo 'Unknown - reasons to be confirmed';	break;
					
					endswitch;	
				?></td>
			</tr>
			
			<?php else : ?>
			
				<?php $i = 0; foreach ( $feedback['employer']['stage_1']['interns'] AS $c ) : ?>
				
					<tr>
						<?php if ( $i === 0 ) : ?>
						<td class="label" rowspan="<?=count( $feedback['employer']['stage_1']['interns'] )?>">Candidate Feedback:</td>
						<?php endif; ?>
						<td>
							<p>
								<strong><?=$c->first_name . ' ' . $c->last_name?></strong>
								<span class="right">
									<?=anchor( 'admin/accounts/edit/' . $c->user_id, 'Profile', 'class="a-button a-button-small" target="_parent"' )?>
								</span>
							</p>
							<p class="candidate_feedback">
								<span>Invited to interview:</span><?=( $c->interview ) ? 'Yes' : 'No' ?>
							</p>
							<p class="candidate_feedback">
								<span>Offered Placement:</span><?=( $c->placement ) ? 'Yes' : 'No' ?>
							</p>
							<p class="stars candidate_feedback">
								<span>Suitability:</span><?=form_dropdown( 'suitability', $stars, $c->suitability, 'disabled' )?>
							</p>
							<p class="candidate_feedback">
								<span>Notes:</span><?=( empty( $c->notes ) ) ? '&ndash;' : '<em>"'.$c->notes.'"</em>' ?>
							</p>
						</td>
					</tr>
				
				<?php $i++; endforeach ?>
			
			<?php endif ?>
			
			<tr>
				<td class="label"><strong>Additional comments:</strong></td>
				<td><?=( empty( $feedback['employer']['stage_1']['main']->comment ) ) ? '&ndash;' : '<em>"'.$feedback['employer']['stage_1']['main']->comment.'"</em>' ?></td>
			</tr>
		
		</table>
		</div>
	
	<?php endif; ?>
	
	
	
	
	
	
	<!--	STAGE TWO FEEDBACK	-->
	<h2 style="margin-top:30px;">
		Stage Two Feedback
		<?php if ( empty( $feedback['employer']['stage_2']['main'] ) ) : ?>

			<span class="right" style="font-size:0.7em;">No feedback yet submitted</span>

		<?php else : ?>
		
			<a href="#" id="employer_stage_two_table_toggle" class="showhide a-button a-button-small right">Show Feedback</a>
		
		<?php endif; ?>
	</h2>
	
	<?php if ( ! empty( $feedback['employer']['stage_2']['main'] ) ) : ?>
	
		<div id="employer_stage_two_table" style="display:none;">
		<table>
		
			<tr>
			
				<td class="label">Feedback submitted by:</td>
				<td>
					<?=$feedback['employer']['stage_2']['main']->first_name . ' ' . $feedback['employer']['stage_2']['main']->last_name?>
					- <?=nice_time( $feedback['employer']['stage_2']['main']->created )?>
				</td>
			</tr>
			
			<?php $i = 0; foreach ( $feedback['employer']['stage_2']['interns'] AS $c ) : ?>
			
				<tr>
					<?php if ( $i === 0 ) : ?>
					<td class="label" rowspan="<?=count( $feedback['employer']['stage_2']['interns'] )?>">Candidate Feedback:</td>
					<?php endif; ?>
					<td>
						<p>
							<strong><?=$c->first_name . ' ' . $c->last_name?></strong>
							<span class="right">
								<?=anchor( 'admin/accounts/edit/' . $c->user_id, 'Profile', 'class="a-button a-button-small" target="_parent"' )?>
							</span>
						</p>
						<p class="stars candidate_feedback">
							<span>Suitability:</span><?=form_dropdown( 'suitability', $stars, $c->suitability, 'disabled' )?>
						</p>
						<p class="stars candidate_feedback">
							<span>Attitude:</span><?=form_dropdown( 'attitude', $stars, $c->attitude, 'disabled' )?>
						</p>
						<p class="stars candidate_feedback">
							<span>Punctuality:</span><?=form_dropdown( 'punctuality', $stars, $c->punctuality, 'disabled' )?>
						</p>
							<p class="candidate_feedback">
								<span>Will hire:</span><?=( $c->willhire ) ? 'Yes' : 'No' ?>
							</p>
						<p class="candidate_feedback">
							<span>Notes:</span><?=( empty( $c->notes ) ) ? '&ndash;' : '<em>"'.$c->notes.'"</em>' ?>
						</p>
					</td>
				</tr>
			
			<?php $i++; endforeach ?>
			
			<tr>
				<td class="label"><strong>Additional comments:</strong></td>
				<td><?=( empty( $feedback['employer']['stage_2']['main']->comment ) ) ? '&ndash;' : '<em>"'.$feedback['employer']['stage_1']['main']->comment.'"</em>' ?></td>
			</tr>
		
		</table>
		</div>
	
	<?php endif; ?>

</div>


<!--	INTERN FEEDBACK	-->
<div id="content-intern"  class="main" style="display:none;">

	<h2>
		Stage Two Feedback
		<?php if ( empty( $feedback['intern']['stage_2']['main'] ) ) : ?>

			<span class="right" style="font-size:0.7em;">No feedback yet submitted</span>

		<?php else : ?>
		
			<a href="#" id="intern_stage_two_table_toggle" class="showhide a-button a-button-small right">Show Feedback</a>
		
		<?php endif; ?>
	</h2>
	
	<?php if ( ! empty( $feedback['intern']['stage_2']['main'] ) ) : ?>
	
		<div id="intern_stage_two_table" style="display:none;">
		<table>
			
			<?php $i = 0; foreach ( $feedback['intern']['stage_2']['main'] AS $c ) : ?>
			
				<tr>
					<td class="label" rowspan="">
						<?=$c->first_name . ' ' . $c->last_name?>:
						<span>Submitted <?=nice_time( $c->created )?>.</span>
					</td>
					<td>
						<p>
							<span class="right">
								<?=anchor( 'admin/accounts/edit/' . $c->user_id, 'Profile', 'class="a-button a-button-small" target="_parent"' )?>
							</span>
						</p>
						<p class="stars candidate_feedback">
							<span>Experience:</span><?=form_dropdown( 'experience', $stars, $c->experience, 'disabled' )?>
						</p>
						<p class="stars candidate_feedback">
							<span>Atmosphere:</span><?=form_dropdown( 'atmosphere', $stars, $c->atmosphere, 'disabled' )?>
						</p>
						<p class="stars candidate_feedback">
							<span>Training:</span><?=form_dropdown( 'punctuality', $stars, $c->training, 'disabled' )?>
						</p>
						<p class="candidate_feedback">
							<span>Further comments:</span><?=( empty( $c->comments ) ) ? '&ndash;' : '<em>"'.$c->comments.'"</em>' ?>
						</p>
					</td>
				</tr>
			
			<?php $i++; endforeach ?>
		
		</table>
		</div>
	
	<?php endif; ?>

</div>