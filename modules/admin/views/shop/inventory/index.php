<div class="group-shop inventory browse">
	<p>
		Browse the shop's inventory.
		<?php

			if ( user_has_permission( 'admin.shop.inventory_create' ) ) :

				echo anchor( 'admin/shop/inventory/import', 'Import Items', 'class="awesome small orange right"' );
				echo anchor( 'admin/shop/inventory/create', 'Add New Item', 'class="awesome small green right"' );

			endif;

		?>
	</p>

	<?php

		$this->load->view( 'admin/shop/inventory/utilities/search' );
		$this->load->view( 'admin/shop/inventory/utilities/pagination' );

	?>

	<table>
		<thead>
			<tr>
				<th class="id">ID</th>
				<th class="image">Image</th>
				<th class="active">Active</th>
				<th class="title">Title &amp; Description</th>
				<th class="type">Type</th>
				<th class="datetime">Modified</th>
				<th class="actions">Actions</th>
			</tr>
		</thead>
		<tbody>
			<?php

				if ( $items->data ) :

					foreach ( $items->data AS $item ) :

						?>
						<tr id="product-<?=$item->id?>">
							<td class="id"><?=number_format( $item->id )?></td>
							<td class="image">
								<?php

									if ( ! empty( $item->gallery[0] ) ) :

										echo anchor( cdn_serve( $item->gallery[0] ), img( cdn_scale( $item->gallery[0], 75, 75 ) ), 'class="fancybox"' );

									else :

										echo img( NAILS_ASSETS_URL . 'img/admin/modules/shop/image-icon.png' );

									endif;

								?>
							</td>

							<?php

							if ( $item->is_active ) :

								echo '<td class="status success">';
									echo '<span class="ion-checkmark-circled"></span>';
								echo '</td>';

							else :

								echo '<td class="status error">';
									echo '<span class="ion-close-circled"></span>';
								echo '</td>';

							endif;

							?>

							<td class="title">
								<?=$item->title?>
								<small><?=word_limiter( strip_tags( $item->description ), 30 )?></small>
							</td>
							<td class="type"><?=$item->type->label?></td>
							<?php

								$this->load->view( 'admin/_utilities/table-cell-datetime',	array( 'datetime' => $item->modified ) );

							?>
							<td class="actions">
								<?php

									//	Render buttons
									$_buttons = array();

									if ( user_has_permission( 'admin.shop.edit' ) ) :

										$_buttons[] = anchor( 'admin/shop/inventory/edit/' . $item->id, lang( 'action_edit' ), 'class="awesome small"' );

									endif;

									// --------------------------------------------------------------------------

									if ( user_has_permission( 'admin.shop.delete' ) ) :

										$_buttons[] = anchor( 'admin/shop/inventory/delete/' . $item->id, lang( 'action_delete' ), 'class="awesome small red confirm" data-title="Are you sure?" data-body="You cannot undo this action"' );

									endif;

									// --------------------------------------------------------------------------

									if ( $_buttons ) :

										foreach ( $_buttons aS $button ) :

											echo $button;

										endforeach;

									else :

										echo '<span class="blank">There are no actions you can perform on this item.</span>';

									endif;

								?>
							</td>
						</tr>
						<?php

					endforeach;

				else :
					?>
					<tr>
						<td colspan="7" class="no-data">
							<p>No Orders found</p>
						</td>
					</tr>
					<?php
				endif;

			?>
		</tbody>
	</table>
	<?php

		$this->load->view( 'admin/shop/inventory/utilities/pagination' );

	?>
</div>