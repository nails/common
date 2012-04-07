<!--	CUSTOM CSS	-->
<style type="text/css">
	
	em { font-style:italic; }
	td.vars, td.options { width: 100px; text-align:center; }
	td#no_records { height: 75px; text-align: center; color: #aaa; text-transform: uppercase; }
	.vars_note { font-weight:bold;padding-bottom:10px;margin-bottom:10px;border-bottom:1px dashed #ccc; }
	.img
	{
		width:40px;
		height:40px;
		float:left;
		margin-right:7px;
		margin-top:4px;
	}
	
	/*	Fixes preview CSS bug	*/
	body { padding:0px !important; }

</style>

<!--	CUSTOM JS-->
<script type="text/javascript">
<!--// Hide from old browsers
	
	$(function() {
	
		$( 'a.fancybox' ).fancybox({type:'iframe', width: '90%', height:'90%'});
		$( 'a.delete' ).click( function() {
		
			return confirm( 'Are you sure? This cannot be undone.' );
		
		});
	
	});
	
//-->
</script>


<h1>Sent Messages</h1>
<p>Below are the last 50 emails which have been sent by the system, view the details of each item by clicking the 'View' button in the 'Variables' column.</p>
<p><em>Note: these are individual mailshots which were sent, depending on users preferences these may have been merged into a single email (a daily digest for example).</em></p>

<section class="filter-box">
	<p style="margin:0;padding:0;">
		<form method="get" action="<?=site_url( 'admin/emails/sent' )?>" class="form" style="margin:0;padding:0;">
		<label>Search:</label>
		<input type="text" name="search" value="<?php if ( $this->input->get( 'search' ) ) : echo $this->input->get( 'search' ); endif;?>">
		<input type="image" src="<?=site_url( 'assets/app/img/icons/search.png' )?>" style="vertical-align:middle">
		</form>
	</p>
</section>

<table>

	<thead>
	
		<tr>
			
			<th>ID</th>
			<th>Recipient</th>
			<th>Type</th>
			<th>Date sent</th>
			<th>Variables</th>
			<th>Options</th>
		
		</tr>
	
	</thead>
	
	<tbody>
	
		<?php if ( count( $sent_mail ) ) : ?>
		<?php foreach ( $sent_mail AS $e ) : ?>
	
			<tr>
			
				<td><?=number_format( $e->id )?></td>
				<td>
					
					<div class="img">
					<?php
					
						if ( $e->profile_img ) :
						
							echo anchor( CDN_SERVER . 'profile_images/' . $e->profile_img,  img( cdn_thumb( 'profile_images', $e->profile_img, 35, 35 ) ), 'class="fancybox"' );
						
						else :
						
							echo img( cdn_placeholder( 35, 35, 1 ) );
						
						endif;
					
					?>
					</div>
					
					<?=anchor( 'admin/accounts/edit/' . $e->user_id, $e->first_name . ' ' . $e->last_name )?>
					<br /><small><?=$e->to?></small>
					
				</td>
				<td><?=$e->name?></td>
				<td><?=nice_time( strtotime( $e->date_archived ) )?></td>
				<td class="vars">
				
					<?php if ( ! empty( $e->email_vars ) ) : ?>
					
						<a href="#vars_<?=$e->id?>" class="fancybox a-button a-button-small">View</a>
						<div style="display:none">
							<div id="vars_<?=$e->id?>" style="padding:20px;">
								<p class="vars_note">This data will be passed to the email manager when the queue is executed.</p>
								<pre><?=print_r( unserialize( $e->email_vars ), TRUE )?></pre>
							</div>
						</div>
					
					<?php else : ?>
					
						<span style="color:#ccc;">&mdash;</span>
					
					<?php endif; ?>
				</td>
				<td class="options">
					
					<?=anchor( 'admin/emails/sent/preview/' . $e->id, 'Preview', 'class="fancybox a-button a-button-small"' )?>
														
				</td>
			
			</tr>
		
		<?php endforeach; ?>
		<?php else: ?>
	
			<tr>
				<td colspan="6" id="no_records">
				
					<p>No records found</p>
				
				</td>
			</tr>
		
		<?php endif;?>
	
	</tbody>

</table>