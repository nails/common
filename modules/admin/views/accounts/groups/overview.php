<!--	PAGE TITLE	-->
<section>
	<h1>Groups</h1>
</section>

<!--	OVERRIDE STYLES	-->
<style type="text/css">
				
	th.id, td.id			{ width: 45px }				
	th.first, td.first		{ width: 125px }
	th.last, td.last		{ width: 125px }
	th.email, td.email		{ width: auto }
	th.group,td.group		{ width: 70px }
	th.options, td.options	{ width: 100px; }
	td#no_records			{ height: 75px; text-align: center; color: #aaa; text-transform: uppercase; }
	code					{ font-family: monospace;}
			
</style>

<!--	START RENDERING TABLE	-->
<section>		
	<table id="group_list">
	
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
				
				<!--	GROUP_ID	-->
				<?php $col = "id"; ?>
				<?php $sortmode = ( $order_col == $col && $order_dir == 'asc' ) ? 'desc' : 'asc' ?>
				<th class="id">
					<?php
						
						if ( $order_dir == 'asc' )
							$img = ( $order_col == $col )	? img( $asc_active )	: img( $desc_inactive );
						
						if ( $order_dir == 'desc' )
							$img = ( $order_col == $col )	? img( $desc_active )	: img( $desc_inactive );
					
					?>
					<?=anchor( 'admin/accounts/groups/' . $col . '/' . $sortmode . $search, 'ID' . $img )?>
				</th>
				
				
				<!--	GROUP_NAME	-->
				<?php $col = "name"; ?>
				<?php $sortmode = ( $order_col == $col && $order_dir == 'asc' ) ? 'desc' : 'asc' ?>
				<th class="first">
					<?php
						
						if ( $order_dir == 'asc' )
							$img = ( $order_col == $col )	? img( $asc_active )	: img( $desc_inactive );
						
						if ( $order_dir == 'desc' )
							$img = ( $order_col == $col )	? img( $desc_active )	: img( $desc_inactive );
					
					?>
					<?=anchor( 'admin/accounts/groups/' . $col . '/' . $sortmode . $search, 'Group Name' . $img )?>
				</th>
				
				
				<!--	DESCRIPTION	-->
				<?php $col = "description"; ?>
				<?php $sortmode = ( $order_col == $col && $order_dir == 'asc' ) ? 'desc' : 'asc' ?>
				<th class="last">
					<?php
						
						if ( $order_dir == 'asc' )
							$img = ( $order_col == $col )	? img( $asc_active )	: img( $desc_inactive );
						
						if ( $order_dir == 'desc' )
							$img = ( $order_col == $col )	? img( $desc_active )	: img( $desc_inactive );
					
					?>
					<?=anchor( 'admin/accounts/groups/' . $col . '/' . $sortmode . $search, 'Description' . $img )?>
				</th>
				
				<!--	DEFAULT_HOMEPAGE	-->
				<?php $col = "default_homepage"; ?>
				<?php $sortmode = ( $order_col == $col && $order_dir == 'asc' ) ? 'desc' : 'asc' ?>
				<th class="last">
					<?php
						
						if ( $order_dir == 'asc' )
							$img = ( $order_col == $col )	? img( $asc_active )	: img( $desc_inactive );
						
						if ( $order_dir == 'desc' )
							$img = ( $order_col == $col )	? img( $desc_active )	: img( $desc_inactive );
					
					?>
					<?=anchor( 'admin/accounts/groups/' . $col . '/' . $sortmode . $search, 'Default Homepage' . $img )?>
				</th>
			
			</tr>
		
		</thead>
		<!--	/TABLE HEAD-->
		
		
		<!--	LIST USERS	-->
		<tbody>
		
			<?php if ( count( $groups ) == 0 ) : ?>
			
				<tr>
					<td colspan="5" id="no_records">
					
						<p>No records found</p>
					
					</td>
				</tr>
			
			<?php else : ?>
			
				<?php foreach ( $groups AS $g ) : ?>
			
				<tr>
				
					<td class="id"><?=number_format( $g->id )?></td>
					<td class="name"><?=( empty( $g->name ) )	? '<span style="color:#ccc;"> &nbsp;&mdash;</span>' : ucwords( str_replace( '_', ' ', $g->name ) ) ?></td>
					<td class="descrption"><?=( empty( $g->description ) )	? '<span style="color:#ccc;"> &nbsp;&mdash;</span>' : $g->description ?></td>
					<td class="default_homepage"><?=( empty( $g->default_homepage ) ) ? '<span style="color:#ccc;"> &nbsp;&mdash;</span>' : '<code>' . $g->default_homepage . '</code>' ?></td>
				
				</tr>
				
				<?php endforeach; ?>
				
			<?php endif; ?>
		
		</tbody>
		<!--	/LIST USERS	-->
	
	</table>
	
</section>

<div class="clear"></div>