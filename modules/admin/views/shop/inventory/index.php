<div class="group-shop inventory browse">
	<p>
		Browse the shop's inventory.
		<?php

			if ( $user->has_permission( 'admin.shop.inventory_create' ) ) :

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

									if ( ! isset( $item->gallery[0] ) ) :

										echo img( cdn_scale( $item->gallery[0], 64, 64 ) );

									else :

										echo img( NAILS_URL . 'img/admin/modules/shop/image-icon.png' );

									endif;

								?>
							</td>
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

									if ( $user->has_permission( 'admin.shop.edit' ) ) :

										$_buttons[] = anchor( 'admin/shop/inventory/edit/' . $item->id, lang( 'action_edit' ), 'class="awesome small"' );

									endif;

									// --------------------------------------------------------------------------

									if ( $user->has_permission( 'admin.shop.delete' ) ) :

										$_buttons[] = anchor( 'admin/shop/inventory/delete/' . $item->id, lang( 'action_delete' ), 'class="awesome small red confirm" data-confirm="Are you sure?"' );

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