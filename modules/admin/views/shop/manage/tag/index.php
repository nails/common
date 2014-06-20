<div class="group-shop manage tags overview">
	<?php

		if ( $is_fancybox ) :

			echo '<h1>' . $page->title . '</h1>';
			$_class = 'system-alert';

		else :

			$_class = '';

		endif;

	?>
	<p class="<?=$_class?>">
		Manage the which tags are available for your products. Tags help the shop determine related products.
	</p>
	<?=$is_fancybox ? '' : '<hr />'?>
	<ul class="tabs disabled">
		<li class="tab active">
			<?=anchor( 'admin/shop/manage/tag' . $is_fancybox, 'Overview' )?>
		</li>
		<li class="tab">
			<?=anchor( 'admin/shop/manage/tag/create' . $is_fancybox, 'Create Tag' )?>
		</li>
	</ul>
	<section class="tabs pages">
		<div class="tab page active">
			<table>
				<thead>
					<tr>
						<th class="label">Label &amp; Description</th>
						<th class="count">Products</th>
						<th class="modified">Modified</th>
						<th class="actions">Actions</th>
					</tr>
				</thead>
				<tbody>
				<?php

					if ( $tags ) :

						foreach( $tags AS $tag ) :

							echo '<tr>';
								echo '<td class="label">';

								echo $tag->label;
								echo $tag->description ? '<small>' . character_limiter( strip_tags( $tag->description ), 225 ) . '</small>' : '<small>No Description</small>';

								echo '</td>';
								echo '<td class="count">';
									echo ! isset( $tag->product_count ) ? 'Unknown' : $tag->product_count;
								echo '</td>';
								echo $this->load->view( '_utilities/table-cell-datetime', array( 'datetime' => $tag->modified ), TRUE );
								echo '<td class="actions">';

									if ( user_has_permission( 'admin.shop.tag_edit' ) ) :

										echo anchor( 'admin/shop/manage/tag/edit/' . $tag->id . $is_fancybox, lang( 'action_edit' ), 'class="awesome small"' );

									endif;

									if ( user_has_permission( 'admin.shop.tag_delete' ) ) :

										echo anchor( 'admin/shop/manage/tag/delete/' . $tag->id . $is_fancybox, lang( 'action_delete' ), 'class="awesome small red confirm" data-title="Are you sure?" data-body="This action cannot be undone."' );

									endif;

								echo '</td>';
							echo '</tr>';

						endforeach;

					else :

						echo '<tr>';
							echo '<td colspan="4" class="no-data">';
								echo 'No Tags, add one!';
							echo '</td>';
						echo '</tr>';

					endif;

				?>
				</tbody>
			</table>
		</div>
	</section>
</div>
<?php

	$this->load->view( 'admin/shop/manage/tag/_footer' );