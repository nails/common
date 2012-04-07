<?php

	$method = $this->uri->segment( 3, 'index' );
	
?>

<!--	CUSTOM JS	-->
<script type="text/javascript">
<!--// Hide from old browsers
	
	$(function() {
	
		//	Generic Fancybox
		$( 'a.fancybox' ).fancybox();
		
		//	CV Fancys
		$( 'a.fancybox-preview' ).fancybox({
		'padding': 10,
		'type':'iframe',
		'width': $(window).width(),
		'height': $(window).height(),
		'centerOnScroll': false,
		'overlayOpacity': 0.6,
		'overlayColor': '#000'
		});
		
		$('.confirm-delete').click(function(){
		
			var ok = confirm ('Are you sure you wish to delete this internship? This will remove all traces of this internship from the database, as well as any associated intern opt-ins or opt-outs.');
		
			if (ok) {
				return true;
			}else{
				return false;
			}
			
			return false;
		});
	
	});
	
//-->
</script>

<!--	OVERRIDE STYLES	-->
<style type="text/css">
				
	th.job_title, td.job_title			{ width: 180px }
	th.company_name, td.company_name	{ width: 100px }
	th.sector, td.sector	{ width: 100px }
	th.date_added, td.date_added		{ width: 90px }
	th.date_start, td.date_start		{ width: 90px }
	th.date_deadline, td.date_deadline	{ width: 90px }
	th.options, td.options				{ width: 50px; text-align: center; }
	td.order_details					{ width: 80px; text-align: center; }
	td.preview							{ color: #e7e7e7; text-align: center;width:60px; }
	td#no_records						{ height: 75px; text-align: center; color: #aaa; text-transform: uppercase; }
			
</style>


<?php if ( $this->uri->segment( 3 ) != 'smart_lists' ) : ?>
<section class="filter-box">
	<p style="margin:0;padding:0;">
		<form method="get" action="<?=site_url( 'admin/internships/' . $method )?>" class="form" style="margin:0;padding:0;">
		<label>Search:</label>
		<input type="text" name="search" value="<?php if ( $this->input->get('search') ) : echo $this->input->get('search'); endif;?>">
		<input type="image" src="/assets/app/img/icons/search.png" style="vertical-align:middle">
		</form>
	</p>
</section>
<?php endif; ?>

<hr>


<?php
	
	$order_col	= $pagination->order->column;
	$order_dir	= $pagination->order->direction;
	
	$asc_active		= APPPATH . 'modules/admin/views/_assets/img/sort/asc-active.png';
	$asc_inactive	= APPPATH . 'modules/admin/views/_assets/img/sort/asc-inactive.png';
	$desc_active	= APPPATH . 'modules/admin/views/_assets/img/sort/desc-active.png';
	$desc_inactive	= APPPATH . 'modules/admin/views/_assets/img/sort/desc-inactive.png';
	$search			= ( isset( $page->search ) && $page->search !== FALSE) ? '?search=' . urlencode( $page->search ) : NULL;
	
	if ( isset( $page->filter ) ) :
		if ( $search ) :
			$filter = '&';
		else :
			$filter = '?';
		endif;
		$filter .= "filter";
		foreach ( $page->filter AS $table => $term ) :
			$filter .= '[' . $table . ']=' . $term;
		endforeach;
	else:
		$filter = NULL;
	endif;

?>

<section>

<table>

	<thead>
	
		<tr>
				
			<!--	USER_ID	-->
			<?php $col = "job_title"; ?>
			<?php $sortmode = ( $order_col == $col && $order_dir == 'asc' ) ? 'desc' : 'asc' ?>
			<th class="<?=$col?>">
				<?php
					
					if ( $order_dir == 'asc' )
						$img = ( $order_col == $col )	? img( $asc_active )	: img( $desc_inactive );
					
					if ( $order_dir == 'desc' )
						$img = ( $order_col == $col )	? img( $desc_active )	: img( $desc_inactive );
				
				?>
				<?=anchor( 'admin/internships/' . $method . '/' . $col . '/' . $sortmode . $search, 'Title' . $img )?>
			</th>
			
			<?php $col = "company_name"; ?>
			<?php $sortmode = ( $order_col == $col && $order_dir == 'asc' ) ? 'desc' : 'asc' ?>
			<th class="<?=$col?>">
				<?php
					
					if ( $order_dir == 'asc' )
						$img = ( $order_col == $col )	? img( $asc_active )	: img( $desc_inactive );
					
					if ( $order_dir == 'desc' )
						$img = ( $order_col == $col )	? img( $desc_active )	: img( $desc_inactive );
				
				?>
				<?=anchor( 'admin/internships/' . $method . '/' . $col . '/' . $sortmode . $search, 'Employer' . $img )?>
			</th>


			<?php $col = "sector"; ?>
			<?php $sortmode = ( $order_col == $col && $order_dir == 'asc' ) ? 'desc' : 'asc' ?>
			<th class="<?=$col?>">
				<?php
					
					if ( $order_dir == 'asc' )
						$img = ( $order_col == $col )	? img( $asc_active )	: img( $desc_inactive );
					
					if ( $order_dir == 'desc' )
						$img = ( $order_col == $col )	? img( $desc_active )	: img( $desc_inactive );
				
				?>
				<?=anchor( 'admin/internships/' . $method . '/' . $col . '/' . $sortmode . $search, 'Sector' . $img )?>
			</th>
			
			<?php $col = "date_added"; ?>
			<?php $sortmode = ( $order_col == $col && $order_dir == 'asc' ) ? 'desc' : 'asc' ?>
			<th class="<?=$col?>">
				<?php
					
					if ( $order_dir == 'asc' )
						$img = ( $order_col == $col )	? img( $asc_active )	: img( $desc_inactive );
					
					if ( $order_dir == 'desc' )
						$img = ( $order_col == $col )	? img( $desc_active )	: img( $desc_inactive );
				
				?>
				<?=anchor( 'admin/internships/' . $method . '/' . $col . '/' . $sortmode . $search, 'Added' . $img )?>
			</th>

		
			
			<?php $col = "date_deadline"; ?>
			<?php $sortmode = ( $order_col == $col && $order_dir == 'asc' ) ? 'desc' : 'asc' ?>
			<th class="<?=$col?>">
				<?php
					
					if ( $order_dir == 'asc' )
						$img = ( $order_col == $col )	? img( $asc_active )	: img( $desc_inactive );
					
					if ( $order_dir == 'desc' )
						$img = ( $order_col == $col )	? img( $desc_active )	: img( $desc_inactive );
				
				?>
				<?=anchor( 'admin/internships/' . $method . '/' . $col . '/' . $sortmode . $search, 'Deadline' . $img )?>
			</th>
		
			<?php $col = "date_start"; ?>
			<?php $sortmode = ( $order_col == $col && $order_dir == 'asc' ) ? 'desc' : 'asc' ?>
			<th class="<?=$col?>">
				<?php
					
					if ( $order_dir == 'asc' )
						$img = ( $order_col == $col )	? img( $asc_active )	: img( $desc_inactive );
					
					if ( $order_dir == 'desc' )
						$img = ( $order_col == $col )	? img( $desc_active )	: img( $desc_inactive );
				
				?>
				<?=anchor( 'admin/internships/' . $method . '/' . $col . '/' . $sortmode . $search, 'Starts' . $img )?>
			</th>			
						
			<th class="order_details">Order Ref</th>
			<th class="options"></th>
			<th class="options"></th>
		
		</tr>
	
	</thead>

	<tbody>
	
	<?php if ( count( $internships ) == 0 ) : ?>
	
		<tr>
			<td colspan="9" id="no_records">
			
				<p>No records found</p>
			
			</td>
		</tr>
	
	<?php endif; ?>
	
	<?php foreach ( $internships AS $i ) : ?>

		<tr>
			<td>
				<strong><a href="<?=site_url( 'admin/internships/edit/' . $i->id )?>"><span class="search_me"><?=$i->job_title?></span></a></strong>
				<br>
				<small><?=word_limiter($i->job_description,10)?></small>
			</td>
			<td>
				<?=anchor( 'admin/employers/edit/' . $i->company, $i->company_name )?>
			</td>
			<td>
				<?=anchor( '/admin/lists/sector_edit/'.$i->sector_id.'/', $i->sector )?>
			</td>
			
			<td>
				<?=reformat_date($i->date_added, "jS M y")?>
				<br>
				<small><?=reformat_date($i->date_added, "d/m/Y")?></small>
			</td>
			<td>
				<?php
					if ( $i->date_deadline == '0000-00-00' ): echo '-'; else: echo reformat_date($i->date_deadline, "jS M y") . '<br><small>'.reformat_date($i->date_deadline, "d/m/Y").'</small>'; endif;
				?>
			</td>
			<td>
				<?=reformat_date($i->date_start, "jS M y")?>
				<br>
				<small><?=reformat_date($i->date_start, "d/m/Y")?></small>
			</td>

			<td class="order_details">
				<?php
					if ( $i->order_id ) :
						echo anchor( 'admin/orders/edit/' . $i->order_id, $i->order_ref );
					endif;
				?>
			</td>
			
			<td class="options">
				<?php 
					if ( $i->active == 1 ) :
					
						echo anchor( 'admin/internships/edit/'.$i->id, 'Active', 'class="a-button a-button-green a-button-small"');
					
					else :
					
						echo '<span style="color:#ccc;">&mdash;</span>';
					
					endif;
				?>
			</td>

			<td class="options">
				<?=anchor( 'admin/internships/edit/' . $i->id, 'Edit', 'class="a-button a-button-small"' )?>
				<?php
					if ( $i->active == 1 ) : echo anchor( 'internships/opportunity/'.$i->id.'/'.$i->company_url_id , 'Preview', 'class="fancybox-preview a-button a-button-small"'); endif;
				?>
				<?php if ( ! $i->order_id ) : ?>
					<?=anchor( 'admin/internships/delete/' . $i->id, 'Delete', 'class="a-button a-button-small confirm-delete"')?>
				<?php endif; ?>						
			</td>
		
		</tr>
	
	<?php endforeach; ?>
	
	</tbody>

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
				echo '<li class="previous start">'	. anchor( 'admin/internships/' . $method . '/' . $order_col . '/' . $order_dir . '/0' . $search . $filter, '&laquo;', 'class="a-button"' ) . '</li>';
				echo '<li class="previous">'		. anchor( 'admin/internships/' . $method . '/' . $order_col . '/' . $order_dir . '/' . $prev . $search . $filter, '&lsaquo;', 'class="a-button"' ) . '</li>';
			
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
				
				echo '<li class="next">'		. anchor( 'admin/internships/' . $method . '/' . $order_col . '/' . $order_dir . '/' . $next . $search . $filter, '&rsaquo;', 'class="a-button"' ) . '</li>';
				echo '<li class="next end">'	. anchor( 'admin/internships/' . $method . '/' . $order_col . '/' . $order_dir . '/' . $pagination->num_pages . $search . $filter, '&raquo;', 'class="a-button"' ) . '</li>';
			
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