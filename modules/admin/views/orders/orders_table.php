<?php
	
	//	Define method so links are correct
	$method = $this->uri->segment( 3, 'index' );
	
	$order_col	= $pagination->order->column;
	$order_dir	= $pagination->order->direction;

?>

<!--	CUSTOM CSS	-->
<style type="text/css">
				
	th.id, td.id			{ width: 45px }
	th.options, td.options	{ width: 150px; text-align: center; }
	td#no_records			{ height: 75px; text-align: center; color: #aaa; text-transform: uppercase; }
	
	/*	Status Colours	*/
	.status span span
	{
		background: #efefef;
		width: 20px;
		height: 20px;
		display: block;
		float: left;
		margin-right: 10px;
		border-radius: 4px;
		border:1px solid #777;
	}
	.status span img
	{
		-moz-box-shadow: none;
		-webkit-box-shadow: none;
		box-shadow: none;
	}
	.status .pending span		{ background: #000; }
	.status .processing span	{ background: url( <?=site_url( '/assets/app/img/loader-lightgreen.gif' )?> ) center no-repeat, lightgreen; }
	.status .closing span		{ background: orange; }
	.status .closed span		{ background: darkorange; }
	.status .ready span			{ background: #66CC00; }
	.status .complete span		{ background: green; }
	.status .declined span		{ background: red; }
	.status .cancelled span		{ background: red; }
	.status .unknown span		{ background: red; }
	
</style>

<!--	CUSTOM JS	-->
<script type="text/javascript">
<!--// Hide from old browsers
	
	$(function() {
	
		$( 'a.cancel' ).click( function() { return confirm( 'Are you sure you wish to cancel this order?' ); } );
		
		//	Default Fancyboxes
		$('.fancybox').fancybox({
			'centerOnScroll' : false,
		});
		
		//	Feedback fancybox
		$('.fancybox-feedback').fancybox({
			'centerOnScroll' : false,
			'width' : '90%',
			'height' : '90%',
			'type' : 'iframe'
		});
	
	});
	
//-->
</script>


<!--	START RENDERING TABLE	-->
<section>		
	<table id="order_list">
	
		<!--	TABLE HEAD	-->
		<thead>
		
			<tr>
				<?php
					
					$asc_active		= APPPATH . 'modules/admin/views/_assets/img/sort/asc-active.png';
					$asc_inactive	= APPPATH . 'modules/admin/views/_assets/img/sort/asc-inactive.png';
					$desc_active	= APPPATH . 'modules/admin/views/_assets/img/sort/desc-active.png';
					$desc_inactive	= APPPATH . 'modules/admin/views/_assets/img/sort/desc-inactive.png';
					$search			= ( isset( $page->search ) && $page->search !== FALSE) ? '?search=' . urlencode( $page->search ) : NULL;
										
				?>
								
				
				
				<!--	INTERNSHIP TITLE	-->
				<?php $col = "i.job_title"; ?>
				<?php $sortmode = ( $order_col == $col && $order_dir == 'asc' ) ? 'desc' : 'asc' ?>
				<th class="first">
					<?php
						
						if ( $order_dir == 'asc' )
							$img = ( $order_col == $col )	? img( $asc_active )	: img( $desc_inactive );
						
						if ( $order_dir == 'desc' )
							$img = ( $order_col == $col )	? img( $desc_active )	: img( $desc_inactive );
					
					?>
					<?=anchor( 'admin/orders/' . $method . '/' . $col . '/' . $sortmode . $search , 'Internship' . $img )?>
				</th>
				
								
				
				<!--	EMPLOYER	-->
				<?php $col = "e.name"; ?>
				<?php $sortmode = ( $order_col == $col && $order_dir == 'asc' ) ? 'desc' : 'asc' ?>
				<th class="first">
					<?php
						
						if ( $order_dir == 'asc' )
							$img = ( $order_col == $col )	? img( $asc_active )	: img( $desc_inactive );
						
						if ( $order_dir == 'desc' )
							$img = ( $order_col == $col )	? img( $desc_active )	: img( $desc_inactive );
					
					?>
					<?=anchor( 'admin/orders/' . $method . '/' . $col . '/' . $sortmode . $search , 'Employer' . $img )?>
				</th>
				
								
				<!--	STATUS	-->
				<?php $col = "o.status"; ?>
				<?php $sortmode = ( $order_col == $col && $order_dir == 'asc' ) ? 'desc' : 'asc' ?>
				<th class="last">
					<?php
						
						if ( $order_dir == 'asc' )
							$img = ( $order_col == $col )	? img( $asc_active )	: img( $desc_inactive );
						
						if ( $order_dir == 'desc' )
							$img = ( $order_col == $col )	? img( $desc_active )	: img( $desc_inactive );
					
					?>
					<?=anchor( 'admin/orders/' . $method . '/' . $col . '/' . $sortmode . $search , 'Order Status' . $img )?>
				</th>
				
				
				<!--	DATE_CREATED	-->
				<?php $col = "o.date_created"; ?>
				<?php $sortmode = ( $order_col == $col && $order_dir == 'asc' ) ? 'desc' : 'asc' ?>
				<th class="email">
					<?php
						
						if ( $order_dir == 'asc' )
							$img = ( $order_col == $col )	? img( $asc_active )	: img( $desc_inactive );
						
						if ( $order_dir == 'desc' )
							$img = ( $order_col == $col )	? img( $desc_active )	: img( $desc_inactive );
					
					?>
					<?=anchor( 'admin/orders/' . $method . '/' . $col . '/' . $sortmode . $search , 'Order Placed' . $img )?>
				</th>
				
				
				<!--	OPTIONS	-->
				<th class="options">Options</th>
			
			</tr>
		
		</thead>
		<!--	/TABLE HEAD-->
		
		
		<!--	LIST ORDERS	-->
		<tbody>
		
			<?php if ( count( $orders ) == 0 ) : ?>
			
				<tr>
					<td colspan="7" id="no_records">
					
						<p>No records found</p>
					
					</td>
				</tr>
			
			<?php else : $return_string = '?return_to=' . urlencode( uri_string() . '?' . $_SERVER['QUERY_STRING'] ); ?>
			
				<?php foreach ( $orders AS $o ) : ?>
			
				<tr>
				
					<td class="internship">
						
						<?=$o->ref?>
						<br>
						<?=anchor( 'admin/internships/edit/' . $o->internship_id . $return_string, $o->job_title )?>
						
					</td>
					<td class="employer">
						<?=anchor( 'admin/employers/edit/' . $o->employer_id . $return_string, $o->employer_name )?><br>
						<?=anchor( 'admin/accounts/edit/' . $o->creator_id . $return_string, title_case( $o->creator_first . ' ' . $o->creator_last ) )?>
					</td>
					<td class="status">
					<?php
					
						switch( $o->status ) :
						
							case 1 :	echo '<span class="pending"><span>' .	 img( 'assets/app/img/status_glare.png' ). '</span>Pending</span>';		break;
							case 2 :	echo '<span class="processing"><span>' . img( 'assets/app/img/status_glare.png' ). '</span>Processing</span>';	break;
							case 3 :	echo '<span class="closing"><span>' .	 img( 'assets/app/img/status_glare.png' ). '</span>Closing</span>';		break;
							case 4 :	echo '<span class="closed"><span>' .	 img( 'assets/app/img/status_glare.png' ). '</span>Closed</span>';		break;
							case 5 :	echo '<span class="ready"><span>' .		 img( 'assets/app/img/status_glare.png' ). '</span>Ready</span>';		break;
							case 6 :	echo '<span class="complete"><span>' .	 img( 'assets/app/img/status_glare.png' ). '</span>Complete</span>';	break;
							case 7 :	echo '<span class="declined"><span>' .	 img( 'assets/app/img/status_glare.png' ). '</span>Declined</span>';	break;
							case 8 :	echo '<span class="cancelled"><span>' .	 img( 'assets/app/img/status_glare.png' ). '</span>Cancelled</span>';	break;
							default :	echo '<span class="unknown"><span>' .	 img( 'assets/app/img/status_glare.png' ). '</span>Unknown</span>';		break;
						
						endswitch;
					
					?>
					</td>
					<td class="date_created"><?=nice_time( $o->date_created )?></td>
					<td class="options">
						
						<?php
													
							//	Show different options depending on order status
							switch ( $o->status ) :
							
								/**
								 * 
								 * PENDING
								 * Edit | Decline | Start Processing
								 * 
								 **/
								case 1 :
								
									/*	echo anchor( 'admin/orders/view/' . $o->id . $return_string, 'View', 'class="a-button a-button-small"' );	*/
									echo '<a href="#details_'.$o->id.'" class="a-button a-button-small fancybox">Details</a>';
									echo '<a href="#history_'.$o->id.'" class="a-button a-button-small fancybox">History</a>';
									echo anchor( 'admin/orders/edit/' . $o->id . $return_string, 'Edit', 'class="a-button a-button-small"' );
									echo anchor( 'admin/orders/set_status/7/' . $o->id . $return_string, 'Refund (Decline)', 'class="a-button a-button-small a-button-red"' );
									echo anchor( 'admin/orders/set_status/2/' . $o->id . $return_string, 'Start Processing', 'class="a-button a-button-small a-button-green"' );
								
								break;
								
								
								/**
								 * 
								 * PROCESSING / CLOSING
								 * Show Matches | History | Cancel
								 * 
								 **/
								case 2 :
								case 3 :
								
									echo '<a href="#details_'.$o->id.'" class="a-button a-button-small fancybox">Details</a>';
									echo '<a href="#history_'.$o->id.'" class="a-button a-button-small fancybox">History</a>';
									echo anchor( 'admin/orders/edit/' . $o->id . $return_string, 'Edit', 'class="a-button a-button-small"' );
									echo anchor( 'admin/orders/set_status/8/' . $o->id . $return_string, 'Refund (Cancel)', 'class="a-button a-button-small a-button-red"' );
									echo anchor( 'admin/orders/show_matches/' . $o->id . $return_string, 'Show Matches', 'class="a-button a-button-small"' );

								break;
								
								
								/**
								 * 
								 * CLOSED
								 * Choose Shortlist | History | Complete
								 * 
								 **/
								case 4 :
								
									echo '<a href="#details_'.$o->id.'" class="a-button a-button-small fancybox">Details</a>';
									echo '<a href="#history_'.$o->id.'" class="a-button a-button-small fancybox">History</a>';
									echo anchor( 'admin/orders/edit/' . $o->id . $return_string, 'Edit', 'class="a-button a-button-small"' );
									echo anchor( 'admin/orders/set_status/8/' . $o->id . $return_string, 'Refund', 'class="a-button a-button-small a-button-red"' );
									echo anchor( 'admin/orders/complete_order/' . $o->id . $return_string, 'Choose Shortlist', 'class="a-button a-button-small a-button-green"' );
									//echo anchor( 'admin/orders/history/' . $o->id . $return_string, 'History', 'class="a-button a-button-small"' );
								
								break;
								
								
								/**
								 * 
								 * READY 
								 * History
								 * 
								 **/
								case 5 :
								
									echo '<a href="#details_'.$o->id.'" class="a-button a-button-small fancybox">Details</a>';
									echo '<a href="#history_'.$o->id.'" class="a-button a-button-small fancybox">History</a>';
									echo anchor( 'admin/orders/edit/' . $o->id . $return_string, 'Edit', 'class="a-button a-button-small"' );
									echo anchor( 'admin/orders/set_status/8/' . $o->id . $return_string, 'Refund', 'class="a-button a-button-small a-button-red"' );
								
								break;
								
								
								/**
								 * 
								 * COMPLETE
								 * Revisit Shortlist | History
								 * 
								 **/
								case 6 :
								
									echo '<a href="#details_'.$o->id.'" class="a-button a-button-small fancybox">Details</a>';
									echo '<a href="#history_'.$o->id.'" class="a-button a-button-small fancybox">History</a>';
									echo anchor( 'admin/orders/feedback/' . $o->id, 'Feedback', 'class="a-button a-button-small fancybox-feedback"' );
									echo anchor( 'admin/orders/edit/' . $o->id . $return_string, 'Edit', 'class="a-button a-button-small"' );
									echo anchor( 'admin/orders/set_status/8/' . $o->id . $return_string, 'Refund', 'class="a-button a-button-small a-button-red"' );
									echo anchor( 'admin/orders/show_matches/' . $o->id . $return_string, 'Revisit Matches', 'class="a-button a-button-small"' );
								
								break;
								
								
								/**
								 * 
								 * DECLINED / CANCELLED
								 * History
								 * 
								 **/
								case 7 :
								case 8 :
								
									echo '<a href="#details_'.$o->id.'" class="a-button a-button-small fancybox">Details</a>';
									echo '<a href="#history_'.$o->id.'" class="a-button a-button-small fancybox">History</a>';
									echo anchor( 'admin/orders/edit/' . $o->id . $return_string, 'Edit', 'class="a-button a-button-small"' );
								
								break;
							
							
							endswitch;
							
						?>
						
						<!--	FANCYBOXES	-->
						<div style="display:none;">
						
							<div id="history_<?=$o->id?>" style="width:95%;padding:20px;margin:auto;">
							
								<h1>Order History</h1>
							
								<ul class="widget-list">
							
								<?php foreach ( $o->history AS $h ) : ?>
								
									<?php //if ( $h->type == 'processing' ) : continue; endif; ?>
								
									<li class="history">
									
										<strong><?php 
																		
											switch ( $h->type )  :
											
												case 'create':
													echo 'Order Created, Pending Approval';
													break;
												case 'processing':
													echo $h->note;
													break;
												case 'new_interns':
													//echo 'Processing New Matches';
													echo $h->note;
													break;
												case 'opt_in_manual':
													echo 'A Potential Candidate Manually Opted In';
													break;
												case 'opt_in_confirm':
													echo 'A Matched Candidate Confirmed Availability';
													break;
												case 'opt_in_decline':
													echo 'A Matched Candidate Opted-Out';
													break;
												case 'closing':
													echo 'Order Closing';
													break;
												case 'closed':
													echo 'Order Closed, Pending Approval';
													break;
												case 'intern_purchased':
													echo 'Intern Purchased!';
													break;
												case 'ready':
													echo 'Order Ready!';
													break;
												case 'complete':
													echo 'Order Complete!';
													break;
												case 'declined':
													echo 'This order has been declined and your credits refunded.';
													break;
												case 'cancelled':
													echo 'This order has been cancelled and your credits refunded.';
													break;
												default:
													echo $h->type;
													break;
											endswitch;
											
										?></strong>
										<span class="indicator">
											<?=reformat_date($h->date)?>
										</span>
										
									</li>
								
								<?php endforeach; ?>
							
								</ul>
							
							</div>
		
							<div id="details_<?=$o->id?>" style="width:95%;padding:20px;margin:auto;">
							
								<h1><strong><?=$o->job_title?></strong></h1>
								<p><?=$o->job_description?></p>
							
								<ul class="widget-list">
									<li class="closing"><strong><?php if($o->date_added!='0000-00-00') : echo reformat_date($o->date_added, 'jS F Y'); else: echo "N/A"; endif; ?></strong> <span class="indicator">Order Placed</span> </li>
									<li class="person"><strong><?=$o->first_name?> <?=$o->last_name?></strong> <span class="indicator">Placed By</span> </li>
									<li class="pending"><strong><?php if($o->date_deadline!='0000-00-00') : echo reformat_date($o->date_deadline, 'jS F Y'); else: echo "Open"; endif; ?></strong> <span class="indicator">Order Deadline</span> </li>
									<li class="date"><strong><?php if($o->date_start!='0000-00-00') : echo reformat_date($o->date_start, 'jS F Y'); else: echo "Unknown"; endif; ?></strong> <span class="indicator">Start Date</span> </li>
									<li class="date"><strong><?php if($o->duration!='') : echo $o->duration . ' ' . $o->duration_term; else: echo "Unknown"; endif; ?></strong> <span class="indicator">Duration</span> </li>
									<li class="processing"><span class="indicator">Industry</span> <strong><?php if ( $o->sector_short ) : echo $o->sector_short; else: echo $o->sector; endif;?></strong></li>
									<li class="map"><span class="indicator">Location</span> <strong><?=$o->location_name;?></strong></li>
									<li class="history"><span class="indicator">Reference</span> <strong><?php if($o->input_ref!='') : echo strtoupper($o->input_ref); else: echo 'IA-' . $o->internship_id; endif;?></strong></li>
									<li class="money"><span class="indicator">Rate of Pay</span> <strong><?php if($o->pay_rate!='') : echo '&pound;' . number_format($o->pay_rate, 2); else: echo 'On Request'; endif;?></strong></li>
									<li class="money"><span class="indicator">Frequency of Pay</span> <strong><?php if($o->pay_frequency!='') : echo ucfirst($o->pay_frequency); else: echo 'On Request'; endif;?></strong></li>
								</ul>
																	
							</div>
						
						</div>
						
					</td>
				
				</tr>
				
				<?php endforeach; ?>
				
			<?php endif; ?>
		
		</tbody>
		<!--	/LIST ORDER	-->
	
	</table>
	
</section>
	
<aside>

	<!--	PAGINATION	-->
	<?php
	
	if ( isset( $pagination ) ) :
	
	?>
	<ul class="pagination">
	
		<?php
		
			if ( $pagination->page != 0 ) :
			
				$prev = ( $pagination->page - 1 >= 0 ) ? $pagination->page-1 : 0;
				echo '<li class="previous start">'	. anchor( 'admin/orders/' . $method . '/' . $order_col . '/' . $order_dir . '/0' . $search , '&laquo;', 'class="a-button"' ) . '</li>';
				echo '<li class="previous">'		. anchor( 'admin/orders/' . $method . '/' . $order_col . '/' . $order_dir . '/' . $prev . $search, '&lsaquo;', 'class="a-button"' ) . '</li>';
			
			else :
			
				echo '<li class="previous start disabled"><a href="#" class="a-button" onclick="return false;">&laquo;</a></li>';
				echo '<li class="previous disabled"><a href="#" class="a-button" onclick="return false;">&lsaquo;</a></li>';
				
			endif;
			
		?>
		
		<li class="info">Page <strong><?php $pagination->page++; echo $pagination->page;?></strong> / <?=( $pagination->num_pages == 0 ) ? 1 : $pagination->num_pages?></li>
		
		
		<?php
		
			if  ($pagination->page != $pagination->num_pages && $pagination->num_pages != 0 ) :
				
				$next = ( $pagination->page < $pagination->num_pages ) ? $pagination->page : $pagination->num_pages - 1;
				$pagination->num_pages--;
				
				echo '<li class="next">'		. anchor( 'admin/orders/' . $method . '/' . $order_col . '/' . $order_dir . '/' . $next . $search, '&rsaquo;', 'class="a-button"' ) . '</li>';
				echo '<li class="next end">'	. anchor( 'admin/orders/' . $method . '/' . $order_col . '/' . $order_dir . '/' . $pagination->num_pages . $search, '&raquo;', 'class="a-button"' ) . '</li>';
			
			else :
			
				echo '<li class="next end disabled"><a href="#" class="a-button" onclick="return false;">&rsaquo;</a></li>';
				echo '<li class="next disabled"><a href="#"  class="a-button" onclick="return false;">&raquo;</a></li>';
	
			endif;
			
		?>
	</ul>
	<?php endif; ?>
</aside>

<!--	CLEARFIX -->
<div class="clear"></div>