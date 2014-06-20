<div class="group-cms blocks overview">

	<p>
		<?php

			if ( user_has_permission( 'admin.cms.can_create_block' ) ) :

				echo anchor( 'admin/cms/blocks/create', 'Add New Block', 'class="awesome small green right"' );

			endif;

		?>
	</p>
	<p>
		Blocks allow you to update a single block of content. Blocks might appear in more than one place so any updates will be reflected across
		all instances.
	</p>

	<hr />

	<div class="search">
		<div class="search-text">
			<input type="text" name="search" value="" autocomplete="off" placeholder="Search block titles by typing in here...">
		</div>
	</div>

	<hr />

	<table>
		<thead>
			<tr>
				<th class="title">Block Title &amp; Description</th>
				<th class="default">Value</th>
				<th class="actions">Actions</th>
			</tr>
		</thead>
		<tbody>
		<?php

			if ( $blocks ) :

				foreach ( $blocks AS $block ) :

					echo '<tr class="block" data-title="' . $block->title . '">';

					echo '<td class="title">';
					echo '<strong>' . $block->title . '</strong>';
					echo '<small>';
					echo 'Slug: ' . $block->slug . '<br />';
					echo 'Description: ' . $block->description . '<br />';
					echo ( ! empty( $block->located ) ? 'Located: ' . $block->located . '<br />' : NULL );
					echo 'Type: ' . $block_types[$block->type] . '<br />';
					echo '</small>';
					echo '</td>';

					// --------------------------------------------------------------------------

					if ( count( $languages ) == 1 ) :

						echo '<td class="default">';
						echo character_limiter( strip_tags( $block->default_value ), 100 );
						echo '</td>';

					else :

						echo '<td class="default">';
						echo '<ul>';
						foreach ( $block->translations AS $variation ) :

							$_label = ! empty( $languages[$variation->language] ) ? $languages[$variation->language] : $variation->language;

							echo '<li>';
								echo '<strong>' . $_label . ':</strong> ';
								echo character_limiter( strip_tags( $variation->value ), 100 );
							echo '</li>';

						endforeach;
						echo '</ul>';
						echo'</td>';

					endif;

					// --------------------------------------------------------------------------

					echo '<td class="actions">';
					echo anchor( 'admin/cms/blocks/edit/' . $block->id, 'Edit', 'class="awesome small"' );
					echo '</td>';

					echo '</tr>';

				endforeach;

			else :

					echo '<tr>';
					echo '<td colspan="3" class="no-data">';
					echo 'No editable blocks found';
					echo '</td>';
					echo '</tr>';

			endif;

		?>
		</tbody>
	</table>
</div>

<script type="text/javascript">
<!--//

	$(function(){

		var CMS_Blocks = new NAILS_Admin_CMS_Blocks;
		CMS_Blocks.init_search();


	});

//-->
</script>