<?php
	$method = $this->uri->segment( 3, 'index' );
?>
<!--	OVERRIDE STYLES	-->
<style type="text/css">
				
	th.first, td.first		{ width: 125px }
	th.last, td.last		{ width: 125px }
	th.email, td.email		{ width: auto }
	th.group,td.group		{ width: 70px }
	th.options, td.options	{ width: 100px; }
	th.id,td.id				{ width:30px; text-align:center; }
	td.img					{ text-align:center; width:40px; }
	td.img img				{ vertical-align: middle; }
	th.group,td.group		{ text-align:center; }
	td 						{ text-overflow: ellipsis; white-space: nowrap; overflow: hidden; }
			
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

<aside style="padding:20px 0px;">
	<?php
		
		$_url		 = $this->uri->segment( 4 );
		$_url 		= $_url == 0 ? 'NEWEST' : '/' . ( $_url-1);
		$_search	= $this->input->get( 'search' ) ? '?search=' . $this->input->get( 'search' ) : '';
		
		if ( $_url != 'NEWEST' ) :
		
			echo anchor( 'admin/accounts/' . $method . $_url . $_search, '&lsaquo; Newer', 'class="awesome small"' );
			echo ' ';
			echo anchor( 'admin/accounts/' . $method . $_search, '&laquo; Newest', 'class="awesome small"' );
		
		endif;
		
		
		if ( $this->uri->segment( 4 ) + 1 <= $total_pages ) :
		
			$_url = '/' . ( $this->uri->segment( 4 ) + 1 );
			echo anchor( 'admin/accounts/' . $method . $_url . $_search, 'Older &rsaquo;', 'class="right awesome small"' );
			echo anchor( 'admin/accounts/' . $method . '/' . $total_pages . $_search, 'Oldest &raquo;', 'class="right awesome small"' );
		
		endif;
		
	?>
	
	<!--	CLEARFIX	-->
	<div class="clear_right"></div>
</aside>

<!--	START RENDERING TABLE	-->
<section>		
	<table id="account_list">
	
		<!--	TABLE HEAD	-->
		<thead>
			<tr>
				<!--	PROFILE IMG, CV, % COMPLETE	& SCORE	-->
				<th class="id">ID</th>
				<th class="img">&nbsp;</th>
				<th class="first">First Name</th>
				<th class="last">Surname</th>
				<th class="email">Email</th>
				<th class="group">Group</th>
				<th class="options">Options</th>
			</tr>
		</thead>
		<!--	/TABLE HEAD-->
		
		
		<!--	LIST USERS	-->
		<tbody>
		
			<?php if ( count( $users ) == 0 ) : ?>
			
				<tr>
					<td colspan="7" id="no_records">
						<p>No records found</p>
					</td>
				</tr>
			
			<?php else : ?>
			
				<?php foreach ( $users AS $u ) : ?>
			
				<tr>
					<td class="id"><?=number_format( $u->id )?></td>
					<td class="img">
					<?php
					
						if ( ! empty( $u->profile_img ) ) :
						
							echo anchor( cdn_serve( 'profile-images', $u->profile_img ), img( cdn_thumb( 'profile-images', $u->profile_img, 35, 35 ) ), 'class="fancybox"' );
						
						else :
						
							echo img( cdn_blank_avatar( 35, 35 ) );
						
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

<aside style="padding:20px 0px;">
	<?php
		
		$_url		= $this->uri->segment( 4 );
		$_url		= $_url == 0 ? 'NEWEST' : '/' . ( $_url-1);
		$_search	= $this->input->get( 'search' ) ? '?search=' . $this->input->get( 'search' ) : '';
		
		if ( $_url != 'NEWEST' ) :
		
			echo anchor( 'admin/accounts/' . $method . $_url . $_search, '&lsaquo; Newer', 'class="awesome small"' );
			echo ' ';
			echo anchor( 'admin/accounts/' . $method . $_search, '&laquo; Newest', 'class="awesome small"' );
		
		endif;
		
		
		if ( $this->uri->segment( 4 ) + 1 <= $total_pages ) :
		
			$_url = '/' . ( $this->uri->segment( 4 ) + 1 );
			echo anchor( 'admin/accounts/' . $method . $_url . $_search, 'Older &rsaquo;', 'class="right awesome small"' );
			echo anchor( 'admin/accounts/' . $method . '/' . $total_pages . $_search, 'Oldest &raquo;', 'class="right awesome small"' );
		
		endif;
		
	?>
	
	<!--	CLEARFIX	-->
	<div class="clear_right"></div>
	
</aside>