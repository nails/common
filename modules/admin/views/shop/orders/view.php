<div class="group-shop orders view">
	<fieldset id="order-view-basic">
		<legend>Basic Info</legend>
		<?php

			//	ID
			$_field					= array();
			$_field['key']			= 'id';
			$_field['label']		= 'ID';
			$_field['default']		= $order->id;
			$_field['readonly']		= TRUE;

			echo form_field( $_field );

			// --------------------------------------------------------------------------

			//	REF
			$_field					= array();
			$_field['key']			= 'ref';
			$_field['label']		= 'Reference';
			$_field['default']		= $order->ref;
			$_field['readonly']		= TRUE;

			echo form_field( $_field );

			// --------------------------------------------------------------------------

			//	Status
			$_field					= array();
			$_field['key']			= 'status';
			$_field['label']		= 'Order Status';
			$_field['default']		= $order->status;
			$_field['readonly']		= TRUE;

			echo form_field( $_field );

			// --------------------------------------------------------------------------

			//	Name
			$_field					= array();
			$_field['key']			= 'customer_name';
			$_field['label']		= 'Customer Name';
			$_field['default']		= $order->user->first_name . ' ' . $order->user->last_name;
			$_field['readonly']		= TRUE;

			echo form_field( $_field );


			// --------------------------------------------------------------------------

			//	Group
			$_field					= array();
			$_field['key']			= 'customer_group';
			$_field['label']		= 'Customer Group';
			$_field['default']		= $order->user->group->id ? $order->user->group->label : 'Unregistered User';
			$_field['readonly']		= TRUE;

			echo form_field( $_field );

			// --------------------------------------------------------------------------

			//	Email
			$_field					= array();
			$_field['key']			= 'customer_email';
			$_field['label']		= 'Customer Email';
			$_field['default']		= $order->user->email;
			$_field['readonly']		= TRUE;

			echo form_field( $_field );

			// --------------------------------------------------------------------------

			//	Payment Gateway
			$_field					= array();
			$_field['key']			= 'payment_gateway';
			$_field['label']		= 'Checked out using';
			$_field['default']		= $order->payment_gateway->label;
			$_field['readonly']		= TRUE;

			echo form_field( $_field );

			// --------------------------------------------------------------------------

			//	Specific payment gateway variables
			switch ( $order->payment_gateway->slug ) :

				case 'paypal' :

					$_field					= array();
					$_field['key']			= 'pp_txn_id';
					$_field['label']		= 'PayPal Transaction ID';
					$_field['default']		= $order->pp_txn_id;
					$_field['readonly']		= TRUE;

					echo form_field( $_field );

				break;

			endswitch;

		?>
	</fieldset>

	<fieldset id="order-view-totals">
		<legend>Totals</legend>
		<?php

			$_symbol	= html_entity_decode( $order->currency->order->symbol, ENT_COMPAT, 'UTF-8' );
			$_precision	= $order->currency->order->precision;

			//	Shortcut variable for base and order currencies
			$_ocurrency = $order->currency->order->id;
			$_bcurrency = $order->currency->base->id;

			// --------------------------------------------------------------------------

			if ( $order->requires_shipping ) :

				//	Sub Total
				$_field				= array();
				$_field['key']		= 'total_sub';
				$_field['label']	= 'Sub Total';
				$_field['readonly']	= TRUE;
				$_field['default']	= shop_format_price( $order->totals->sub, TRUE, TRUE, $_bcurrency, TRUE );

				if ( $order->currency->order->id !== $order->currency->base->id ) :

					$_field['default'] .= ' (' . shop_format_price( $order->totals->sub_render, TRUE, TRUE, $_ocurrency, TRUE ) . ')';

				endif;

				echo form_field( $_field );

				//	Shipping Total
				$_field				= array();
				$_field['key']		= 'total_shipping';
				$_field['label']	= 'Shipping';
				$_field['readonly']	= TRUE;
				$_field['default']	= shop_format_price( $order->totals->shipping, TRUE, TRUE, $_bcurrency, TRUE );

				if ( $order->currency->order->id !== $order->currency->base->id ) :

					$_field['default'] .= ' (' . shop_format_price( $order->totals->shipping_render, TRUE, TRUE, $_ocurrency, TRUE ) . ')';

				endif;

				echo form_field( $_field );

				// --------------------------------------------------------------------------

				//	Tax
				$_field				= array();
				$_field['key']		= 'total_tax';
				$_field['label']	= 'Tax (items)';
				$_field['readonly']	= TRUE;
				$_field['default']	= shop_format_price( $order->totals->tax_items, TRUE, TRUE, $_bcurrency, TRUE );

				if ( $order->currency->order->id !== $order->currency->base->id ) :

					$_field['default'] .= ' (' . shop_format_price( $order->totals->tax_items_render, TRUE, TRUE, $_ocurrency, TRUE ) . ')';

				endif;

				echo form_field( $_field );


				// --------------------------------------------------------------------------

				//	Tax
				$_field				= array();
				$_field['key']		= 'total_tax';
				$_field['label']	= 'Tax (shipping)';
				$_field['readonly']	= TRUE;
				$_field['default']	= shop_format_price( $order->totals->tax_shipping, TRUE, TRUE, $_bcurrency, TRUE );

				if ( $order->currency->order->id !== $order->currency->base->id ) :

					$_field['default'] .= ' (' . shop_format_price( $order->totals->tax_shipping_render, TRUE, TRUE, $_ocurrency, TRUE ) . ')';

				endif;

				echo form_field( $_field );

				// --------------------------------------------------------------------------

				if ( $order->discount->items ) :

					//	Sub Total
					$_field				= array();
					$_field['key']		= 'discount_items';
					$_field['label']	= 'Discount (items)';
					$_field['readonly']	= TRUE;
					$_field['default']	= shop_format_price( $order->discount->items, TRUE, TRUE, $_bcurrency, TRUE );

					if ( $order->currency->order->id !== $order->currency->base->id ) :

						$_field['default'] .= ' (' . shop_format_price( $order->discount->items_render, TRUE, TRUE, $_ocurrency, TRUE ) . ')';

					endif;

					$_tip = 'Used voucher/gift card \'' . $order->voucher->code . '\'';

					echo form_field( $_field, $_tip );

				endif;

				if ( $order->discount->shipping ) :

					//	Sub Total
					$_field				= array();
					$_field['key']		= 'discount_shipping';
					$_field['label']	= 'Discount (shipping';
					$_field['readonly']	= TRUE;
					$_field['default']	= shop_format_price( $order->discount->shipping, TRUE, TRUE, $_bcurrency, TRUE );

					if ( $order->currency->order->id !== $order->currency->base->id ) :

						$_field['default'] .= ' (' . shop_format_price( $order->discount->shipping, TRUE, TRUE, $_ocurrency, TRUE ) . ')';

					endif;

					$_tip = 'Used voucher/gift card \'' . $order->voucher->code . '\'';

					echo form_field( $_field, $_tip );

				endif;

				// --------------------------------------------------------------------------

				//	Grand Total
				$_field				= array();
				$_field['key']		= 'total_grand';
				$_field['label']	= 'Grand Total';
				$_field['readonly']	= TRUE;
				$_field['default']	= shop_format_price( $order->totals->grand, TRUE, TRUE, $_bcurrency, TRUE );

				if ( $order->currency->order->id !== $order->currency->base->id ) :

					$_field['default'] .= ' (' . shop_format_price( $order->totals->grand, TRUE, TRUE, $_ocurrency, TRUE ) . ')';

				endif;

				echo form_field( $_field );

				// --------------------------------------------------------------------------

				//	Fees
				$_field				= array();
				$_field['key']		= 'total_fee';
				$_field['label']	= 'Payment Gateway Fee';
				$_field['readonly']	= TRUE;
				$_field['default']	= shop_format_price( $order->totals->fees, TRUE, TRUE, $_bcurrency, TRUE );

				echo form_field( $_field );

			else :

				//	Sub Total
				$_field				= array();
				$_field['key']		= 'total_sub';
				$_field['label']	= 'Sub Total';
				$_field['readonly']	= TRUE;
				$_field['default']	= shop_format_price( $order->totals->sub, TRUE, TRUE, $_bcurrency, TRUE );

				if ( $order->currency->order->id !== $order->currency->base->id ) :

					$_field['default'] .= ' (' . shop_format_price( $order->totals->sub_render, TRUE, TRUE, $_ocurrency, TRUE ) . ')';

				endif;

				echo form_field( $_field );

				// --------------------------------------------------------------------------

				//	Tax
				$_field				= array();
				$_field['key']		= 'total_tax';
				$_field['label']	= 'Tax';
				$_field['readonly']	= TRUE;
				$_field['default']	= shop_format_price( $order->totals->tax_items, TRUE, TRUE, $_bcurrency, TRUE );

				if ( $order->currency->order->id !== $order->currency->base->id ) :

					$_field['default'] .= ' (' . shop_format_price( $order->totals->tax_items_render, TRUE, TRUE, $_ocurrency, TRUE ) . ')';

				endif;

				echo form_field( $_field );

				// --------------------------------------------------------------------------

				if ( $order->discount->items ) :

					//	Sub Total
					$_field				= array();
					$_field['key']		= 'discount_items';
					$_field['label']	= 'Discount';
					$_field['default']	= $_symbol . number_format( $order->discount->items, $_precision );
					$_field['readonly']	= TRUE;
					$_field['default']	= shop_format_price( $order->discount->items, TRUE, TRUE, $_bcurrency, TRUE );

					if ( $order->currency->order->id !== $order->currency->base->id ) :

						$_field['default'] .= ' (' . shop_format_price( $order->discount->items_render, TRUE, TRUE, $_ocurrency, TRUE ) . ')';

					endif;

					$_tip = 'Used voucher/gift card \'' . $order->voucher->code . '\'';

					echo form_field( $_field, $_tip );

				endif;

				// --------------------------------------------------------------------------

				//	Grand Total
				$_field				= array();
				$_field['key']		= 'total_grand';
				$_field['label']	= 'Grand Total';
				$_field['readonly']	= TRUE;
				$_field['default']	= shop_format_price( $order->totals->grand, TRUE, TRUE, $_bcurrency, TRUE );

				if ( $order->currency->order->id !== $order->currency->base->id ) :

					$_field['default'] .= ' (' . shop_format_price( $order->totals->grand_render, TRUE, TRUE, $_ocurrency, TRUE ) . ')';

				endif;

				echo form_field( $_field );

				// --------------------------------------------------------------------------

				//	Fees
				$_field				= array();
				$_field['key']		= 'total_fee';
				$_field['label']	= 'Payment Gateway Fee';
				$_field['default']	= $_symbol . number_format( $order->totals->fees, $_precision );
				$_field['readonly']	= TRUE;
				$_field['default']	= shop_format_price( $order->totals->fees, TRUE, TRUE, $_bcurrency, TRUE );

				echo form_field( $_field );

			endif;

			// --------------------------------------------------------------------------

			//	Checkout Currency
			$_field				= array();
			$_field['key']		= 'checkout_currency';
			$_field['label']	= 'Checkout Currency';
			$_field['default']	= $order->currency->order->code;
			$_field['readonly']	= TRUE;

			echo form_field( $_field );

			// --------------------------------------------------------------------------

			//	Base Currency
			$_field				= array();
			$_field['key']		= 'base_currency';
			$_field['label']	= 'Base Currency at time';
			$_field['default']	= $order->currency->base->code;
			$_field['readonly']	= TRUE;

			echo form_field( $_field );

			// --------------------------------------------------------------------------

			//	Exchange Rate
			$_field				= array();
			$_field['key']		= 'exchange_rate';
			$_field['label']	= 'Exchange Rate Used';
			$_field['default']	= $order->currency->exchange_rate;
			$_field['readonly']	= TRUE;

			echo form_field( $_field );

		?>
	</fieldset>

	<?php if ( $order->requires_shipping ) : ?>
	<fieldset id="order-view-shipping">
		<legend>Shipping Details</legend>
		<?php

			//	Addressee
			$_field					= array();
			$_field['key']			= 'addressee';
			$_field['label']		= 'Addressee';
			$_field['default']		= $order->shipping_details->addressee;
			$_field['readonly']		= TRUE;

			echo form_field( $_field );

			// --------------------------------------------------------------------------

			//	Line 1
			$_field					= array();
			$_field['key']			= 'line1';
			$_field['label']		= 'Line 1';
			$_field['default']		= $order->shipping_details->line_1;
			$_field['readonly']		= TRUE;

			echo form_field( $_field );

			// --------------------------------------------------------------------------

			//	Addressee
			$_field					= array();
			$_field['key']			= 'line2';
			$_field['label']		= 'line_2';
			$_field['default']		= $order->shipping_details->line_2;
			$_field['readonly']		= TRUE;

			echo form_field( $_field );

			// --------------------------------------------------------------------------

			//	Town
			$_field					= array();
			$_field['key']			= 'town';
			$_field['label']		= 'Town';
			$_field['default']		= $order->shipping_details->town;
			$_field['readonly']		= TRUE;

			echo form_field( $_field );

			// --------------------------------------------------------------------------

			//	PostCode
			$_field					= array();
			$_field['key']			= 'postcode';
			$_field['label']		= 'PostCode';
			$_field['default']		= $order->shipping_details->postcode;
			$_field['readonly']		= TRUE;

			echo form_field( $_field );

			// --------------------------------------------------------------------------

			//	State
			$_field					= array();
			$_field['key']			= 'state';
			$_field['label']		= 'State';
			$_field['default']		= $order->shipping_details->state;
			$_field['readonly']		= TRUE;

			echo form_field( $_field );

			// --------------------------------------------------------------------------

			//	Country
			$_field					= array();
			$_field['key']			= 'country';
			$_field['label']		= 'Country';
			$_field['default']		= $order->shipping_details->country;
			$_field['readonly']		= TRUE;

			echo form_field( $_field );

			// --------------------------------------------------------------------------

			//	Shipping Method
			$_field					= array();
			$_field['key']			= 'shipping_courier';
			$_field['label']		= 'Shipping Courier';
			$_field['default']		= $order->shipping_method->courier;
			$_field['readonly']		= TRUE;

			echo form_field( $_field );

			// --------------------------------------------------------------------------

			//	Addressee
			$_field					= array();
			$_field['key']			= 'shipping_method';
			$_field['label']		= 'Shipping Method';
			$_field['default']		= $order->shipping_method->method;
			$_field['readonly']		= TRUE;

			echo form_field( $_field );

		?>
	</fieldset>
	<?php endif; ?>

	<fieldset id="order-view-products">
		<legend>Products</legend>
		<table>
			<thead>
				<tr>
					<th class="details">Details</th>
					<th class="quantity">Quantity</th>
					<th class="price">Price</th>
					<?php if ( $order->status == 'PAID' ) : ?>
					<th class="processed">Processed</th>
					<th class="actions">Actions</th>
					<?php endif; ?>
				</tr>
			</thead>
			<tbody>
			<?php

				foreach ( $order->items AS $item ) :

					echo '<tr>';
					echo '<td class="details">';

					if ( $item->was_on_sale ) :

						echo img( array( 'src' => NAILS_URL . 'img/modules/shop/basket/ribbon-on-sale.png', 'class' => 'ribbon' ) );

					endif;

					$this->load->view( 'admin/shop/orders/view-item-cell', array( 'item' => &$item ) );

					echo '</td>';

					// --------------------------------------------------------------------------

					echo '<td class="quantity">' . $item->quantity . '</th>';

					// --------------------------------------------------------------------------

					echo '<td class="price">';

					if ( $item->was_on_sale ) :

						echo shop_format_price( $item->sale_price, TRUE, TRUE, $_bcurrency, TRUE );

						if ( $order->currency->order->id !== $order->currency->base->id ) :

							echo ' (' . shop_format_price( $item->sale_price_render, TRUE, TRUE, $_ocurrency, TRUE ) . ')';

						endif;

						echo '<small>';

							echo 'was ' . shop_format_price( $item->price, TRUE, TRUE, $_bcurrency, TRUE );

							if ( $order->currency->order->id !== $order->currency->base->id ) :

								echo ' (' . shop_format_price( $item->price_render, TRUE, TRUE, $_ocurrency, TRUE ) . ')';

							endif;

						echo '</small>';

					else :

						echo shop_format_price( $item->price, TRUE, TRUE, $_bcurrency, TRUE );

						if ( $order->currency->order->id !== $order->currency->base->id ) :

							echo ' (' . shop_format_price( $item->price_render, TRUE, TRUE, $_ocurrency, TRUE ) . ')';

						endif;

					endif;

					echo '</td>';

					if ( $order->status == 'PAID' ) :

						$_is_fancybox = $this->input->get( 'is_fancybox' ) ? '?is_fancybox=true' : '';

						if ( $item->processed ) :

							echo '<td class="processed yes">' . lang( 'yes' ) . '</td>';

							// --------------------------------------------------------------------------

							echo '<td class="actions">';
							if ( $user->has_permission( 'admin.shop.process' ) ) :

								echo anchor( 'admin/shop/orders/process/' . $order->id . '/' . $item->id . '/unprocessed' . $_is_fancybox, 'Mark as Unprocessed', 'class="awesome small red"' );

							else :

								echo '<span class="blank">&mdash;</span>';

							endif;
							echo '</td>';

						else :

							echo '<td class="processed no">' . lang( 'no' ) . '</td>';

							// --------------------------------------------------------------------------

							echo '<td class="actions">';
							if ( $user->has_permission( 'admin.shop.process' ) ) :

								echo anchor( 'admin/shop/orders/process/' . $order->id . '/' . $item->id . '/processed' . $_is_fancybox, 'Mark as Processed', 'class="awesome small green"' );

							else :

								echo '<span class="blank">&mdash;</span>';

							endif;
							echo '</td>';

						endif;

					endif;

					echo '</tr>';
				endforeach;

			?>
			</tbody>
		</table>
	</fieldset>
</div>

<?php

	if ( $this->input->get( 'is_fancybox' ) && $order->status == 'PAID' ) :

		echo '<script type="text/javascript">';
		if ( $order->fulfilment_status == 'FULFILLED' ) :

			echo 'parent.mark_fulfilled( ' . $order->id . ' );';

		else :

			echo 'parent.mark_unfulfilled( ' . $order->id . ' );';

		endif;
		echo '</script>';

	endif;

?>