<!--	OVERRIDE STYLES	-->
<style type="text/css">
				
	th.first, td.first		{ width: 125px }
	th.last, td.last		{ width: 125px }
	th.email, td.email		{ width: auto }
	th.group,td.group		{ width: 70px }
	th.options, td.options	{ width: 100px; }
	td.profile_img			{ width:40px; }
	td.cv					{ width:40px; text-align: center; color: #e7e7e7; }
	td.percentage			{ width:20px; text-align: center; }
	td.score				{ width:20px; text-align: center; }
	td 						{ text-overflow: ellipsis; white-space: nowrap; overflow: hidden; }
	td#no_records			{ height: 75px; text-align: center; color: #aaa; text-transform: uppercase; }
			
</style>


<section class="filter-box">
	<p>
		<form method="get" action="/admin/accounts/<?=$this->uri->segment(3)?>/">
			<label for="filter-box-search">Search:</label>
			<input type="text" name="search" id="filter-box-search" value="<?=$this->input->get( 'search' )?>" placeholder="Type a search term">
			<input type="image" src="<?=NAILS_URL . 'img/admin/icons/search.png'?>" style="">
		</form>
	</p>
</section>

<hr>

<!--	START RENDERING TABLE	-->
<section>		
	<table id="account_list">
	
		<!--	TABLE HEAD	-->
		<?php if ( ! isset( $order_col ) ) :?>
		<thead>
			<tr>
				<th colspan="img"></td>
				<th class="first">First Name</th>
				<th class="last">Last Name</th>
				<th class="email">Email</th>
				<th class="group">Group</th>
				<th class="options">Options</th>
			</tr>
		</thead>
		<?php else : ?>
		<thead>
		
			<tr>
				
				<!--	PROFILE IMG, CV, % COMPLETE	& SCORE	-->
				<th clas=s"img"></th>
				<th class="first">First Name</th>
				<th class="last">Surname</th>
				<th class="email">Email</th>
				<th class="group">Group</th>
				<th class="options">Options</th>
			
			</tr>
		
		</thead>
		<?php endif; ?>
		<!--	/TABLE HEAD-->
		
		
		<!--	LIST USERS	-->
		<tbody>
		
			<?php if ( count( $users ) == 0 ) : ?>
			
				<tr>
					<td colspan="6" id="no_records">
					
						<p>No records found</p>
					
					</td>
				</tr>
			
			<?php else : ?>
			
				<?php foreach ( $users AS $u ) : ?>
			
				<tr>
				
					<td class="img">
					<?php
					
						if ( ! empty( $u->profile_img ) ) :
						
							echo anchor( CDN_SERVER . 'profile_images/' . $u->profile_img, img( cdn_thumb( 'profile_images', $u->profile_img, 35, 35 ) ), 'class="fancybox"' );
						
						else :
						
							echo img( cdn_placeholder( 35, 35 ) );
						
						endif;
					
					?>
					</td>
					
					<td class="first"><?=( empty( $u->first_name ) )		? '<span style="color:#ccc;"> &nbsp;&mdash;</span>' : title_case( $u->first_name )?></td>
					<td class="last"><?=( empty( $u->last_name ) )			? '<span style="color:#ccc;"> &nbsp;&mdash;</span>' : title_case( $u->last_name )?></td>
					<td class="email"><?=safe_mailto( $u->email )?></td>
					<td class="group"><?=title_case( str_replace( '_', ' ', $u->group_name ) )?></td>
					<td class="options">
						
						<?php
						
							$return_string = '?return_to=' . urlencode( uri_string() . '?' . $_SERVER['QUERY_STRING'] );
							
							if ( array_search( $u->group_id, array( 2, 3, 4, 5 ) ) !== FALSE )
								echo login_as_button( $u->id, $u->password );
							
							//	Edit This User
							echo anchor( 'admin/accounts/edit/' . $u->id . $return_string, 'Edit', 'class="awesome small"' );
							
							
							//	Can't do any of these functions to yourself
							if ( $u->id != active_user( 'id' ) ) :
							
								//echo anchor( 'admin/accounts/delete/' . $u->id . $return_string, 'Delete', 'class="awesome small red"' );
							
								if( ! $u->active )
									echo anchor( 'admin/accounts/activate/' . $u->id . $return_string, 'Activate', 'class="awesome small green"' );
								
								if( $u->active == 2 ) :
								
									echo anchor( 'admin/accounts/unban/' . $u->id . $return_string, 'Unban', 'class="awesome small"' );
									
								else :
								
									echo anchor( 'admin/accounts/ban/' . $u->id . $return_string, 'Ban', 'class="awesome small red"' );
									
								endif;
							
							endif;
							
						?>
						
					</td>
				
				</tr>
				
				<?php endforeach; ?>
				
			<?php endif; ?>
		
		</tbody>
		<!--	/LIST USERS	-->
	
	</table>
	
</section>
	
<aside>

	<!--	PAGINATION	-->
	<?php
	
	if ( isset( $pagination ) ) :
	
		//	Prep the vars
		$order_col	= $pagination->order->column;
		$order_dir	= $pagination->order->direction;
	
	?>
	<ul class="pagination">
	
		<?php
		
			if ( $pagination->page != 0 ) :
			
				$prev = ( $pagination->page - 1 >= 0 ) ? $pagination->page-1 : 0;
				echo '<li class="previous start">'	. anchor( 'admin/accounts/' . $method . '/' . $order_col . '/' . $order_dir . '/0' . $search . $filter, '&laquo;', 'class="awesome"' ) . '</li>';
				echo '<li class="previous">'		. anchor( 'admin/accounts/' . $method . '/' . $order_col . '/' . $order_dir . '/' . $prev . $search . $filter, '&lsaquo;', 'class="awesome"' ) . '</li>';
			
			else :
			
				echo '<li class="previous start disabled"><a href="#" class="awesome" onclick="return false;">&laquo;</a></li>';
				echo '<li class="previous disabled"><a href="#" class="awesome" onclick="return false;">&lsaquo;</a></li>';
				
			endif;
			
		?>
		
		<li class="info">Page <strong><?php $pagination->page++; echo $pagination->page;?></strong> / <?=( $pagination->num_pages == 0 ) ? 1 : $pagination->num_pages?></li>
		
		
		<?php
		
			if  ($pagination->page != $pagination->num_pages && $pagination->num_pages != 0 ) :
				
				$next = ( $pagination->page < $pagination->num_pages ) ? $pagination->page : $pagination->num_pages - 1;
				$pagination->num_pages--;
				
				echo '<li class="next">'		. anchor( 'admin/accounts/' . $method . '/' . $order_col . '/' . $order_dir . '/' . $next . $search . $filter, '&rsaquo;', 'class="awesome"' ) . '</li>';
				echo '<li class="next end">'	. anchor( 'admin/accounts/' . $method . '/' . $order_col . '/' . $order_dir . '/' . $pagination->num_pages . $search . $filter, '&raquo;', 'class="awesome"' ) . '</li>';
			
			else :
			
				echo '<li class="next end disabled"><a href="#" class="awesome" onclick="return false;">&rsaquo;</a></li>';
				echo '<li class="next disabled"><a href="#"  class="awesome" onclick="return false;">&raquo;</a></li>';
	
			endif;
			
		?>
	</ul>
	<?php endif; ?>
	
</aside>

<!--	CLEARFIX -->
<div class="clear"></div>