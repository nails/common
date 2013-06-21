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
							<td class="value"><?=SHOP_BASE_CURRENCY_SYMBOL . number_format( $order->totals->grand, SHOP_BASE_CURRENCY_PRECISION )?></td>
							<td class="status <?=$order->status?>"><?=$order->status?></td>
							<?php

								if ( $order->status == 'PAID' ) :

									if ( $order->fulfilment_status == 'FULFILLED' ) :

										echo '<td class="fulfilment yes">' . lang( 'yes' ) . '</td>';

									else :

										echo '<td class="fulfilment no">' . lang( 'no' ) . '</td>';

									endif;

								else :

									echo '<td class="fulfilment">&mdash;</td>';

								endif;

							?>
							<td class="actions">
								<?=anchor( 'admin/shop/orders/view/' . $order->id, lang( 'action_view' ), 'class="awesome small fancybox" data-fancybox-type="iframe"' )?>
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
	
		$this->load->view( 'admin/shop/orders/utilities/pagination' );
	
	?>
</div>

<script	type="text/javascript">

	function mark_fulfilled( order_id )
	{
		console.log( 'fulfilling ' + order_id );
		$( '#order-' + order_id ).find( 'td.fulfilment' ).removeClass( 'no' ).addClass( 'yes' ).text( '<?=lang( 'yes' )?>' );
	}

	function mark_unfulfilled( order_id )
	{
		console.log( 'unfulfilling ' + order_id );
		$( '#order-' + order_id ).find( 'td.fulfilment' ).removeClass( 'yes' ).addClass( 'no' ).text( '<?=lang( 'no' )?>' );
	}
</script>