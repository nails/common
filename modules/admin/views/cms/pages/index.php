<div class="group-cms pages overview">

	<p>
		Browse editable pages.
		<?php

			if ( user_has_permission( 'admin.cms.can_create_page' ) ) :

				echo anchor( 'admin/cms/pages/create', 'Add New Page', 'class="awesome small green right"' );

			endif;

		?>
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

					$_data = $page->draft;

					// --------------------------------------------------------------------------

					echo '<tr class="page">';
						echo '<td class="title indentosaurus indent-' . $page->draft->depth . '">';

							echo str_repeat( '<div class="indentor"></div>', $page->draft->depth );

							echo '<div class="indentor-content">';

								echo $page->draft->title;

								//	A little feedback on the status of the page:
								//	- If it's in draft state then simply show it's in draft
								//	- If it's published and there are unpublished changes then indicate that

								if ( $page->is_published ) :

									$_published_hash	= ! empty( $page->published->hash )	? $page->published->hash	: 'NOHASH' ;
									$_draft_hash		= ! empty( $page->draft->hash )		? $page->draft->hash		: 'NOHASH' ;

									if ( $_published_hash !== $_draft_hash ) :

										echo anchor( 'admin/cms/pages/edit/' . $page->id, ' <strong rel="tipsy" title="This page is visible on site but changes have been made which have not been published.">(Unpublished Changes)</strong>' );

									endif;

								else :

									echo anchor( 'admin/cms/pages/edit/' . $page->id, ' <strong rel="tipsy" title="This page has not been published. It is not available to your site\'s visitors.">(Unpublished)</strong>' );

								endif;

								$_title_nested = explode( '|', $page->draft->title_nested );
								array_pop( $_title_nested );

								echo '<small>';
								echo $_title_nested ? implode( ' &rsaquo; ', $_title_nested ) : 'Top Level Page';
								echo '</small>';

							echo '</div>';

						echo '</td>';

						$this->load->view( 'admin/_utilities/table-cell-user',		$page->modified_by );
						$this->load->view( 'admin/_utilities/table-cell-datetime',	array( 'datetime' => $page->modified ) );

						echo '<td class="actions">';

							if ( user_has_permission( 'admin.cms.can_edit_page' ) ) :

								echo anchor( 'admin/cms/pages/edit/' . $page->id, lang( 'action_edit' ), 'class="awesome small"' );

							endif;

							//echo anchor( $page->url . '?is_preview=1', lang( 'action_preview' ), 'target="_blank" class="fancybox awesome small green" data-fancybox-type="iframe" data-width="100%" data-height="100%"' );

							if ( user_has_permission( 'admin.cms.can_delete_page' ) ) :

								echo anchor( 'admin/cms/pages/delete/' . $page->id, lang( 'action_delete' ), 'data-title="Are you sure?" data-body="This will remove the page from the site. This action can be undone." class="confirm awesome small red"' );

							endif;

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