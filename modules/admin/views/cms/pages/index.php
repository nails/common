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
				<th class="user">Modified By</th>
				<th class="datetime">Modified</th>
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

					$this->load->view( 'admin/_utilities/table-cell-user',		$page->user );
					$this->load->view( 'admin/_utilities/table-cell-datetime',	array( 'datetime' => $page->modified ) );

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

	<hr />

	<p>
		Sometimes the site's routing system can get confused. If pages aren't appearing as they should try <?=anchor( 'admin/cms/pages/rewrite_routes', 'rewriting the routes file' )?>.
	</p>

</div>

<script type="text/javascript">
<!--//

	$(function(){

		var CMS_Pages = new NAILS_Admin_CMS_Pages;
		CMS_Pages.init_search();


	});

//-->
</script>