<div class="group-shop manage categories overview">
	<?php

		if ( $is_fancybox ) :

			echo '<h1>' . $page->title . '</h1>';
			$_class = 'system-alert';

		else :

			$_class = '';

		endif;

	?>
	<p class="<?=$_class?>">
		Manage the shop's categories. Categories are like departments and should be used to organise
		similar products. Additionally, categories can be nested to more granularly organise items.
	</p>
	<?=$is_fancybox ? '' : '<hr />'?>
	<ul class="tabs disabled">
		<li class="tab active">
			<?=anchor( 'admin/shop/manage/category' . $is_fancybox, 'Overview' )?>
		</li>
		<li class="tab">
			<?=anchor( 'admin/shop/manage/category/create' . $is_fancybox, 'Create Category' )?>
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

					if ( $categories ) :

						foreach( $categories AS $category ) :

							echo '<tr>';
								echo '<td class="label indentosaurus indent-' . $category->depth . '">';

									echo str_repeat( '<div class="indentor"></div>', $category->depth );

									echo '<div class="indentor-content">';

										echo $category->label;

										$_breadcrumbs = array();
										foreach( $category->breadcrumbs AS $bc ) :

											$_breadcrumbs[] = $bc->label;

										endforeach;

										echo $_breadcrumbs ? '<small>' . implode( ' &rsaquo; ', $_breadcrumbs ) . '</small>' : '<small>Top Level Category</small>';
										echo $category->description ? '<small>' . character_limiter( strip_tags( $category->description ), 225 ) . '</small>' : '<small>No Description</small>';

									echo '</div>';

								echo '</td>';
								echo '<td class="count">';
									echo ! isset( $category->product_count ) ? 'Unknown' : $category->product_count;
								echo '</td>';
								echo $this->load->view( '_utilities/table-cell-datetime', array( 'datetime' => $category->modified ), TRUE );
								echo '<td class="actions">';

									if ( user_has_permission( 'admin.shop.category_edit' ) ) :

										echo anchor( 'admin/shop/manage/category/edit/' . $category->id . $is_fancybox, lang( 'action_edit' ), 'class="awesome small"' );

									endif;

									if ( user_has_permission( 'admin.shop.category_delete' ) ) :

										echo anchor( 'admin/shop/manage/category/delete/' . $category->id . $is_fancybox, lang( 'action_delete' ), 'class="awesome small red confirm" data-title="Are you sure?" data-body="This action cannot be undone."' );

									endif;

								echo '</td>';
							echo '</tr>';

						endforeach;

					else :

						echo '<tr>';
							echo '<td colspan="3" class="no-data">';
								echo 'No Categories, add one!';
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

	$this->load->view( 'admin/shop/manage/category/_footer' );