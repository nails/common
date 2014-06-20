<div class="group-shop manage product-type overview">
	<?php

		if ( $is_fancybox ) :

			echo '<h1>' . $page->title . '</h1>';
			$_class = 'system-alert';

		else :

			$_class = '';

		endif;

	?>
	<p class="<?=$_class?>">
		Product types control how the order is processed when a user completes checkout and
		payment is authorised. Most products can simply be considered a generic product, however,
		there are some cases when a product should be processed differently (e.g a download
		requires links to be generated). For the most part product types will be defined by
		the developer, however you may create your own for the sake of organisation in admin.
	</p>
	<?=$is_fancybox ? '' : '<hr />'?>
	<ul class="tabs disabled">
		<li class="tab active">
			<?=anchor( 'admin/shop/manage/product_type' . $is_fancybox, 'Overview' )?>
		</li>
		<li class="tab">
			<?=anchor( 'admin/shop/manage/product_type/create' . $is_fancybox, 'Create Product Type' )?>
		</li>
	</ul>
	<section class="tabs pages">
		<div class="tab page active">
			<table>
				<thead>
					<tr>
						<th class="label">Label &amp; Description</th>
						<th class="ipn">IPN Method</th>
						<th class="physical">Physical</th>
						<th class="max-po">Max Per Order</th>
						<th class="max-v">Max Variations</th>
						<th class="count">Products</th>
						<th class="modified">Modified</th>
						<th class="actions">Actions</th>
					</tr>
				</thead>
				<tbody>
				<?php

					if ( $product_types ) :

						foreach( $product_types AS $product_type ) :

							echo '<tr>';
								echo '<td class="label">';
									echo $product_type->label;
									echo $product_type->description ? '<small>' . character_limiter( strip_tags( $product_type->description ), 225 ) . '</small>' : '<small>No Description</small>';
								echo '</td>';
								echo '<td class="ipn">';
									echo $product_type->ipn_method ? '<code>' . $product_type->ipn_method . '()</code>' : '&mdash;';
								echo '</td>';
								echo '<td class="physical">';
									echo $product_type->is_physical ? lang( 'yes' ) : lang( 'no' );
								echo '</td>';
								echo '<td class="max-po">';
									echo $product_type->max_per_order ? $product_type->max_per_order : 'Unlimited';
								echo '</td>';
								echo '<td class="max-v">';
									echo $product_type->max_variations ? $product_type->max_variations : 'Unlimited';
								echo '</td>';
								echo '<td class="count">';
									echo ! isset( $product_type->product_count ) ? 'Unknown' : $product_type->product_count;
								echo '</td>';
								echo $this->load->view( '_utilities/table-cell-datetime', array( 'datetime' => $product_type->modified ), TRUE );
								echo '<td class="actions">';

									if ( user_has_permission( 'admin.shop.product_type_edit' ) ) :

										echo anchor( 'admin/shop/manage/product_type/edit/' . $product_type->id . $is_fancybox, lang( 'action_edit' ), 'class="awesome small"' );

									endif;

									if ( user_has_permission( 'admin.shop.product_type_delete' ) ) :

										echo anchor( 'admin/shop/manage/product_type/delete/' . $product_type->id . $is_fancybox, lang( 'action_delete' ), 'class="awesome small red confirm" data-title="Are you sure?" data-body="This action cannot be undone."' );

									endif;

								echo '</td>';
							echo '</tr>';

						endforeach;

					else :

						echo '<tr>';
							echo '<td colspan="4" class="no-data">';
								echo 'No Product_types, add one!';
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

	$this->load->view( 'admin/shop/manage/product_type/_footer' );