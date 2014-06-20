<div class="group-shop orders browse">
	<p>
		Browse all orders which have been processed by the site from this page.
	</p>

	<?php

		$this->load->view( 'admin/shop/orders/utilities/search' );
		$this->load->view( 'admin/shop/orders/utilities/pagination' );

	?>

	<table>
		<thead>
			<tr>
				<th class="id">ID</th>
				<th class="ref">Ref</th>
				<th class="datetime">Placed</th>
				<th class="user">Customer</th>
				<th class="value">Value</th>
				<th class="status">Status</th>
				<th class="fulfilment">Fulfilled</th>
				<th class="actions">Actions</th>
			</tr>
		</thead>
		<tbody>
			<?php

				if ( $orders->data ) :

					foreach ( $orders->data AS $order ) :

						?>
						<tr id="order-<?=$order->id?>">
							<td class="id"><?=$order->id?></td>
							<td class="ref"><?=$order->ref?></td>
							<?php

								$this->load->view( 'admin/_utilities/table-cell-datetime',	array( 'datetime' => $order->created ) );
								$this->load->view( 'admin/_utilities/table-cell-user',		$order->user );

							?>
							<td class="value">
							<?php

								echo shop_format_price( $order->totals->grand, TRUE, TRUE, $order->currency->base->id );

								if ( $order->currency->order->id !== $order->currency->base->id ) :

									echo '<small>' . shop_format_price( $order->totals->grand_render, TRUE, TRUE, $order->currency->order->id ) . '</small>';

								endif;

							?>
							</td>
							<td class="status <?=$order->status?>"><?=$order->status?></td>
							<?php

								if ( $order->fulfilment_status == 'FULFILLED' ) :

									echo '<td class="fulfilment yes">' . lang( 'yes' ) . '</td>';

								else :

									echo '<td class="fulfilment no">' . lang( 'no' ) . '</td>';

								endif;

							?>
							<td class="actions">
								<?php

									//	Render buttons
									$_buttons = array();

									// --------------------------------------------------------------------------

									if ( user_has_permission( 'admin.shop.orders_view' ) ) :

										$_buttons[] = anchor( 'admin/shop/orders/view/' . $order->id, lang( 'action_view' ), 'class="awesome small fancybox" data-fancybox-type="iframe"' );

									endif;

									// --------------------------------------------------------------------------

									if ( user_has_permission( 'admin.shop.orders_reprocess' ) ) :

										$_buttons[] = anchor( 'admin/shop/orders/reprocess/' . $order->id, 'Process', 'class="awesome small confirm" data-title="Are you sure?" data-body="Processing the order again may result in multiple dispatch of items."' );

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
						<td colspan="8" class="no-data">
							<p>No Orders found</p>
						</td>
					</tr>
					<?php
				endif;

			?>
		</tbody>
	</table>
	<?php

		$this->load->view( 'admin/shop/orders/utilities/pagination' );

	?>
</div>

<script	type="text/javascript">

	function mark_fulfilled( order_id )
	{
		$( '#order-' + order_id ).find( 'td.fulfilment' ).removeClass( 'no' ).addClass( 'yes' ).text( '<?=lang( 'yes' )?>' );
	}

	function mark_unfulfilled( order_id )
	{
		$( '#order-' + order_id ).find( 'td.fulfilment' ).removeClass( 'yes' ).addClass( 'no' ).text( '<?=lang( 'no' )?>' );
	}
</script>