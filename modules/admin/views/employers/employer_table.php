<?php

	//	Define method so links are correct
	$method = $this->uri->segment( 3, 'index' );
	
	$order_col	= $pagination->order->column;
	$order_dir	= $pagination->order->direction;
	
	$asc_active		= APPPATH . 'modules/admin/views/_assets/img/sort/asc-active.png';
	$asc_inactive	= APPPATH . 'modules/admin/views/_assets/img/sort/asc-inactive.png';
	$desc_active	= APPPATH . 'modules/admin/views/_assets/img/sort/desc-active.png';
	$desc_inactive	= APPPATH . 'modules/admin/views/_assets/img/sort/desc-inactive.png';
	$search			= ( isset( $page->search ) && $page->search !== FALSE) ? '?search=' . urlencode( $page->search ) : NULL;
?>

<!--	CUSTOM CSS	-->
<style type="text/css">
	
	.logo		{}
	.name		{}
	.admins		{}
		.admins span { color: red; font-weight: bold; font-size: 10px; padding-left: 10px; }
	.sectors	{}
	.balance	{ text-align: center; }
	.status		{ width: 50px; }
	.options	{ width: 88px; }
	.manager	{}

</style>

<section>
	
	<section class="filter-box">
		<p style="margin:0;padding:0;">
			<form method="get" action="/admin/employers/<?=$this->uri->segment(3)?>/" class="form" style="margin:0;padding:0;">
			<label>Search:</label>
			<input type="text" name="search" value="<?php if ( $this->input->get('search') ) : echo $this->input->get('search'); endif;?>">
			<input type="image" src="/assets/app/img/icons/search.png" style="vertical-align:middle">
			</form>
		</p>
	</section>
	
	<hr>
	
	<table>
	
		<thead>
		
			<tr>
			
				<th></th>
				
				<!--	EMPLOYER_NAME	-->
				<?php $col = "name"; ?>
				<?php $sortmode = ( $order_col == $col && $order_dir == 'asc' ) ? 'desc' : 'asc' ?>
				<th class="name">
					<?php
						
						if ( $order_dir == 'asc' )
							$img = ( $order_col == $col )	? img( $asc_active )	: img( $desc_inactive );
						
						if ( $order_dir == 'desc' )
							$img = ( $order_col == $col )	? img( $desc_active )	: img( $asc_inactive );
					
					?>
					<?=anchor( 'admin/employers/' . $method . '/' . $col . '/' . $sortmode . $search, 'Employer Details' . $img )?>
				</th>
				<th class="admins">Admins</th>
				<th class="sectors">Sectors</th>
				<th class="balance">Balance</th>
				<th class="status">Status</th>
				<th class="options">Options</th>
			
			</tr>
		
		</thead>
	
		<tbody>
		
		<?php foreach ( $employers AS $e ) : ?>
		
			<tr>
			
				<td class="logo">
					<?php if ( $e->logo ) :
					
						echo img( cdn_scale( 'employer_images/', $e->logo, 60, 60 ) );
						
					endif; ?>
				</td>
				<td class="name">
					<strong><a href="<?=site_url( 'admin/employers/edit/'.$e->id )?>"><span class="search_me"><?=$e->name?></span></a></strong>
					<br>
					<small>Internships: <?=$e->num_positions?></small>
				</td>
				<td class="admins">
					<?php
						if ( $e->admins ) :
							echo '<ul>';
							foreach ( $e->admins AS $s ) :
								echo '<li>';
								echo anchor( 'admin/accounts/edit/' . $s->id, $s->first_name . ' ' . $s->last_name );
								echo ( $s->group_id == 3 ) ? '<span>MANAGER</span>' : NULL;
								echo '</li>';
							endforeach;
							echo '</ul>';
						endif;
					?>
				</td>
				<td class="sectors">
					<?php
						if ( $e->sectors ) :
							echo '<ul>';
							foreach ( $e->sectors AS $s ) :
								echo '<li>' . $s->title . '</li>';
							endforeach;
							echo '</ul>';
						endif;
					?>
				</td>
				<td class="balance">
					<?=number_format( $e->credits )?>
				</td>
				<td  class="status">
					<?php 
						$return_to = urlencode( $this->uri->uri_string() );
						
						if ( $e->active == 1 ) :
						
							echo anchor( 'admin/employers/deactivate/'.$e->id.'?return_to=' . $return_to, 'Deactivate', 'class="a-button a-button-red a-button-small"' );
							
						else:
						
							echo anchor( 'admin/employers/activate/'.$e->id.'?return_to=' . $return_to, 'Activate', 'class="activate a-button a-button-green a-button-small"' );
							
						endif;
					?>
				</td>
	
				<td  class="options">
					
					<a href="<?=site_url( 'admin/employers/edit/' . $e->id )?>" class="a-button a-button-small">Edit</a>
									
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
				echo '<li class="previous start">'	. anchor( 'admin/employers/' . $method . '/' . $order_col . '/' . $order_dir . '/0' . $search , '&laquo;', 'class="a-button"' ) . '</li>';
				echo '<li class="previous">'		. anchor( 'admin/employers/' . $method . '/' . $order_col . '/' . $order_dir . '/' . $prev . $search, '&lsaquo;', 'class="a-button"' ) . '</li>';
			
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
				
				echo '<li class="next">'		. anchor( 'admin/employers/' . $method . '/' . $order_col . '/' . $order_dir . '/' . $next . $search, '&rsaquo;', 'class="a-button"' ) . '</li>';
				echo '<li class="next end">'	. anchor( 'admin/employers/' . $method . '/' . $order_col . '/' . $order_dir . '/' . $pagination->num_pages . $search, '&raquo;', 'class="a-button"' ) . '</li>';
			
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