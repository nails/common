<div class="group-shop manage brands overview">
	<?php

		if ( $is_fancybox ) :

			echo '<h1>' . $page->title . '</h1>';
			$_class = 'system-alert';

		else :

			$_class = '';

		endif;

	?>
	<p class="<?=$_class?>">
		Manage the which attributes are available for your products.
	</p>
	<?=$is_fancybox ? '' : '<hr />'?>
	<ul class="tabs disabled">
		<li class="tab active">
			<?=anchor( 'admin/shop/manage/brands' . $is_fancybox, 'Overview' )?>
		</li>
		<li class="tab">
			<?=anchor( 'admin/shop/manage/brands/create' . $is_fancybox, 'Create Brand' )?>
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
						<th class="active">Active</th>
						<th class="actions">Actions</th>
					</tr>
				</thead>
				<tbody>
				<?php

					if ( $brands ) :

						foreach( $brands AS $brand ) :

							echo '<tr>';
								echo '<td class="label">';

									if ( $brand->logo_id ) :

										echo anchor( cdn_serve( $brand->logo_id), img( cdn_thumb( $brand->logo_id, 32, 32 ) ), 'class="fancybox"' );

									endif;

									echo $brand->label;
									echo $brand->description ? '<small>' . strip_tags( $brand->description ) . '</small>' : '';

								echo '</td>';
								echo '<td class="count">';
									echo ! isset( $brand->product_count ) ? 'Unknown' : $brand->product_count;
								echo '</td>';
								echo $this->load->view( '_utilities/table-cell-datetime', array( 'datetime' => $brand->modified ), TRUE );
								if ( $brand->is_active ) :

									echo '<td class="active success">';
										echo '<span class="ion-checkmark-circled"></span>';
									echo '</td>';

								else :

									echo '<td class="active error">';
										echo '<span class="ion-close-circled"></span>';
									echo '</td>';

								endif;
								echo '<td class="actions">';

									if ( user_has_permission( 'admin.shop.brands_edit' ) ) :

										echo anchor( 'admin/shop/manage/brands/edit/' . $brand->id . $is_fancybox, lang( 'action_edit' ), 'class="awesome small"' );

									endif;

									if ( user_has_permission( 'admin.shop.brands_delete' ) ) :

										echo anchor( 'admin/shop/manage/brands/delete/' . $brand->id . $is_fancybox, lang( 'action_delete' ), 'class="awesome small red confirm" data-title="Are you sure?" data-body="This action cannot be undone."' );

									endif;

								echo '</td>';
							echo '</tr>';

						endforeach;

					else :

						echo '<tr>';
							echo '<td colspan="4" class="no-data">';
								echo 'No Brands, add one!';
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

	$this->load->view( 'admin/shop/manage/brands/_footer' );