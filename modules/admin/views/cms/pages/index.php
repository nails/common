<div class="group-cms pages overview">

	<p>
		Listed below are all the editable pages on site. You use this page manager to edit page content or to change page layout.
	</p>
	
	<hr />
	
	<div class="search">
		<div class="search-text">
			<input type="text" name="search" value="" autocomplete="off" placeholder="Search page titles by typing in here...">
		</div>
	</div>
	
	<hr />
	
	<table>
		<thead>
			<tr>
				<th class="title">Page</th>
				<th class="modified">Modified</th>
				<th class="actions">Actions</th>
			</tr>
		</thead>
		<tbody>
		<?php
		
			if ( $pages ) :
			
				foreach ( $pages AS $page ) :
				
					echo '<tr class="page" data-title="' . $page->title . '">';
					echo '<td class="title">';
					echo '<span class="title">' . $page->title . '</span>';
					echo '<span class="url">' . site_url( $page->slug ) . '</span>';
					echo '</td>';
					echo '<td class="modified">';
					echo '<span class="nice-time">' . $page->modified . '</span>';
					echo $page->user->id ? '<small>by ' . anchor( 'admin/accounts/edit/' . $page->user->id, $page->user->first_name . ' ' . $page->user->last_name ) . '</small>' : '';
					echo '</td>';
					echo '<td class="actions">';
					
					echo anchor( 'admin/cms/pages/edit/' . $page->id, 'Edit', 'class="awesome small"' );
					echo anchor( $page->slug, 'View', 'target="_blank" class="awesome small"' );
					
					echo '</td>';
					echo '</tr>';
				
				endforeach;
			
			else :
			
					echo '<tr>';
					echo '<td colspan="4" class="no-data">';
					echo 'No editable pages found';
					echo '</td>';
					echo '</tr>';
			
			endif;
		
		?>
		</tbody>
	</table>
	
</div>

<script style="text/javascript">
<!--//

	$(function(){
	
		var CMS_Pages = new NAILS_Admin_CMS_Pages;
		CMS_Pages.init_search();
		
	
	});

//-->
</script>