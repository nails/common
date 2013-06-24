<div class="group-shop inventory browse">
	<p>
		Browse the shop's inventory.
	</p>

	<?php
	
		$this->load->view( 'admin/shop/inventory/utilities/search' );
		$this->load->view( 'admin/shop/inventory/utilities/pagination' );
	
	?>

	<table>
		<thead>
			<tr>
				<th class="id">ID</th>
				<th class="title">Title</th>
				<th class="type">Type</th>
				<th class="tax-rate">Tax Rate</th>
				<th class="price">Price</th>
				<th class="sold-available">Sold/Available</th>
				<th class="on-sale">On Sale</th>
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
							<td class="title">
								<?=$item->title?>
								<small><?=$item->description?></small>
							</td>
							<td class="type"><?=$item->type->label?></td>
							<td class="tax-rate">
								<?php

									if ( $item->tax->label ) :

										echo $item->tax->label;

									else :

										echo '<span class="no-data">No Tax Rate</span>';

									endif;

								?>
							</td>
							<td class="price"><?=shop_format_price( $item->price, TRUE )?></td>
							<td class="sold-available">
								<?php

									if ( is_null( $item->quantity_available ) ) :

										echo $item->quantity_sold . '/<span class="infinity">∞</span>';

									else :

										echo $item->quantity_sold . '/' . $item->quantity_available;

									endif;

								?>
							</td>
							<td class="on-sale">
							<?= $item->is_on_sale ? '<span class="yes">' . lang( 'yes' ) . '</span>' : '<span class="no">' . lang( 'no' ) . '</span>'?>
							</td>
							<?php

								$this->load->view( 'admin/_utilities/table-cell-datetime',	array( 'datetime' => $item->modified ) );

							?>
							<td class="actions">
								<?php

									echo anchor( 'admin/shop/inventory/view/' . $item->id, lang( 'action_edit' ), 'class="awesome small fancybox" data-fancybox-type="iframe"' );
									echo anchor( 'admin/shop/inventory/delete/' . $item->id, lang( 'action_delete' ), 'class="awesome small red confirm" date-confirm="Are you sure?"' );

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