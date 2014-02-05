<table class="default-style">
	<thead>
		<tr>
			<th>Item</th>
			<th class="center">Quantity</th>
			<th class="center">Unit Price</th>
			<th class="center">Tax Rate</th>
			<?php if ( $order->requires_shipping ) :?>
			<th class="center">Shipping</th>
			<?php endif; ?>
			<th class="center">Total</th>
		</tr>
	</thead>
	<tbody>
	<?php

		//	Shortcut variable for base and order currencies
		$_ocurrency = $order->currency->order->id;
		$_bcurrency = $order->currency->base->id;

		foreach ( $order->items AS $item ) :

			echo '<tr class="line-bottom">';
			echo '<td>';

			//	Load the 'details' view; in a separate view so apps can easily customise the layout/content
			//	of this part of the view without having to duplicate the entire basket view.

			$this->load->view( 'email/shop/utilities/order_table_item_cell', array( 'item' => &$item ) );

			echo '</td>';
			echo '<td class="center">' . $item->quantity . '</td>';

			if ( $item->was_on_sale ) :

				echo '<td class="center">' . shop_format_price( $item->sale_price_render, TRUE, TRUE, $_ocurrency ) . '</td>';

			else :

				echo '<td class="center">' . shop_format_price( $item->price_render, TRUE, TRUE, $_ocurrency ) . '</td>';

			endif;

				echo '<td class="center">' . $item->tax_rate->rate *100 . '%</td>';

			if ( $order->requires_shipping ) :

				if ( $item->shipping ) :

					echo '<td class="center">' . shop_format_price( $item->shipping_render, TRUE, TRUE, $_ocurrency ) . '</td>';

				else :

					echo '<td class="center">FREE</td>';

				endif;

			endif;

			echo '<td class="center">' . shop_format_price( $item->total_render, TRUE, TRUE, $_ocurrency ) . '</td>';

			echo '</tr>';

		endforeach;

	?>
	<tr>
		<td colspan="4" class="right"><strong>Sub Total</strong></td>
		<?php if ( $order->requires_shipping ) : ?>
		<td class="center"><?=shop_format_price( $order->totals->shipping_render, TRUE, TRUE, $_ocurrency )?></td>
		<?php endif; ?>
		<td class="center"><?=shop_format_price( $order->totals->sub_render, TRUE, TRUE, $_ocurrency )?></td>
	</tr>
	<tr>
		<td colspan="4" class="right"><strong>Tax</strong></td>
		<?php if ( $order->requires_shipping ) : ?>
		<td class="center"><?=shop_format_price( $order->totals->tax_shipping_render, TRUE, TRUE, $_ocurrency )?></td>
		<?php endif; ?>
		<td class="center"><?=shop_format_price( $order->totals->tax_items_render, TRUE, TRUE, $_ocurrency )?></td>
	</tr>
	<?php

		if ( $order->discount->shipping || $order->discount->items ) :

			echo '<tr>';
			echo '<td colspan="4" class="right"><strong>Discounts</strong></td>';
			if ( $order->requires_shipping && $order->discount->shipping ) :

				echo '<td class="center">' . shop_format_price( $item->discount->shipping_render, TRUE, TRUE, $_ocurrency ) . '</td>';

			elseif( $order->requires_shipping ) :

				echo '<td class="center">&mdash;</td>';

			endif;

			if ( $order->discount->items ) :

				echo '<td class="center">' . shop_format_price( $item->discount->items_render, TRUE, TRUE, $_ocurrency ) . '</td>';

			else :

				echo '<td class="center">&mdash;</td>';

			endif;
			echo '</tr>';

		endif;

	?>
	<tr>
		<td colspan="4" class="right"><strong>Grand Total</strong></td>
		<?php if ( $order->requires_shipping ) : ?>
		<td class="center">&nbsp;</td>
		<?php endif; ?>
		<td class="center"><?=shop_format_price( $order->totals->grand_render, TRUE, TRUE, $_ocurrency )?></td>
	</tr>
	</tbody>
</table>

<?php

	if ( $order->voucher ) :

		?>
		<p>
			The following voucher was used with this order:
		</p>
		<p class="heads-up">
			<strong style="padding-right:15px;margin-right:10px;border-right:1px solid #CCC"><?=$order->voucher->code?></strong><?=$order->voucher->label?>
		</p>
		<?php

	endif;

	// --------------------------------------------------------------------------

	if ( $order->requires_shipping ) :

		if ( $type == 'receipt' ) :

			echo '<p>The items in your order which require shipping will be shipped to the following address:</p>';

		elseif ( $type == 'notification' ) :

			echo '<p>The items in the order which require shipping must be shipped to the following address:</p>';

		endif;

		?>
		<table style="width:100%;padding:10px;border:1px solid #CCC;">
			<tr>
				<td>
					<ul style="margin:0;">
						<li style="font-size:1.2em;"><strong><?=$order->shipping_details->addressee?></strong></li>
						<?=$order->shipping_details->line_1 ? '<li>' . $order->shipping_details->line_1 . '</li>' : '' ?>
						<?=$order->shipping_details->line_2 ? '<li>' . $order->shipping_details->line_2 . '</li>' : '' ?>
						<?=$order->shipping_details->town ? '<li>' . $order->shipping_details->town . '</li>' : '' ?>
						<?=$order->shipping_details->postcode ? '<li>' . $order->shipping_details->postcode . '</li>' : '' ?>
						<?=$order->shipping_details->country ? '<li>' . $order->shipping_details->country . '</li>' : '' ?>
						<?=$order->shipping_details->state ? '<li>' . $order->shipping_details->state . '</li>' : '' ?>
					</ul>
				</td>
				<td align="right" valign="top">
					<?=img( NAILS_ASSETS_URL . 'img/modules/shop/email/post-mark.png' )?>
				</td>
			</tr>
		</table>
		<?php

		if ( $type == 'receipt' ) :

			$_track_token = urlencode( $this->encrypt->encode( $order->ref . '|' . $order->id . '|' . time(), APP_PRIVATE_KEY ) );

			echo '<p>';
			echo 'They will be shipped using <strong>' . $order->shipping_method->courier . ' - ' . $order->shipping_method->method . '</strong>; you can also ';
			echo anchor( shop_setting( 'shop_url' ) . 'order/track?token=' . $_track_token , 'track the status of your order' ) . '.';
			echo '</p>';

		elseif ( $type == 'notification' ) :

			echo '<p>';
			echo 'They must be shipped using <strong>' . $order->shipping_method->courier . ' - ' . $order->shipping_method->method . '</strong>.';
			echo '</p>';

		endif;


	endif;

?>