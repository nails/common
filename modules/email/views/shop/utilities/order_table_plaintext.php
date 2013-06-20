<?php

	foreach ( $order->items AS $item ) :

		//	Load the 'details' view; in a separate view so apps can easily customise the layout/content
		//	of this part of the view without having to duplicate the entire basket view.

		$this->load->view( 'email/shop/utilities/order_table_item_cell_plaintext', array( 'item' => &$item ) );

		echo 'Quantity: ' . $item->quantity . "\n";
		
		if ( $item->was_on_sale ) :
		
			echo 'Price: ' . $order->currency->order->symbol . number_format( $item->sale_price, $order->currency->order->precision ) . "\n";
			
		else :
		
			echo 'Price: ' . $order->currency->order->symbol . number_format( $item->price, $order->currency->order->precision ) . "\n";
		
		endif;
		
		echo 'Tax Rate: ' . $item->tax_rate->rate*100 . "%\n";
		
		if ( $order->requires_shipping ) :

			if ( $item->shipping ) :
			 
				echo 'Shipping: ' . $order->currency->order->symbol . number_format( $item->shipping, $order->currency->order->precision ) . "\n";
				
			else :
			
				echo 'Shipping: FREE' . "\n";
			
			endif;

		endif;

		echo 'Price: ' . $order->currency->order->symbol . number_format( $item->total, $order->currency->order->precision ) . "\n";
		
		echo "\n";
	
	endforeach;

	// --------------------------------------------------------------------------

	if ( $order->requires_shipping ) :

		echo 'SUB TOTAL' . "\n";
		echo 'Shipping: ' . $order->currency->order->symbol . number_format( $order->totals->shipping, $order->currency->order->precision ) . "\n";
		echo 'Items: ' . $order->currency->order->symbol . number_format( $order->totals->sub, $order->currency->order->precision ) . "\n";
		echo "\n";
		echo 'TAX' . "\n";
		echo 'Shipping: ' . $order->currency->order->symbol . number_format( $order->totals->tax_shipping, $order->currency->order->precision ) . "\n";
		echo 'Items: ' . $order->currency->order->symbol . number_format( $order->totals->tax_items, $order->currency->order->precision ) . "\n";

		if ( $order->discount->shipping || $order->discount->items ) :

			echo "\n";
			echo 'DISCOUNTS' . "\n";

			if ( $order->discount->shipping ) :

				echo 'Shipping: ' . $order->currency->order->symbol . number_format( $order->discount->shipping, $order->currency->order->precision ) . "\n";

			endif;

			if ( $order->discount->items ) :

				echo 'Items: ' . $order->currency->order->symbol . number_format( $order->discount->items, $order->currency->order->precision ) . "\n";

			endif;

		endif;

	else :

		echo 'SUB TOTAL' . "\n";
		echo 'Items: ' . $order->currency->order->symbol . number_format( $order->totals->sub, $order->currency->order->precision ) . "\n";
		echo "\n";
		echo 'TAX' . "\n";
		echo 'Items: ' . $order->currency->order->symbol . number_format( $order->totals->tax_items, $order->currency->order->precision ) . "\n";

		if ( $order->discount->items ) :

			echo "\n";
			echo 'DISCOUNTS' . "\n";

			if ( $order->discount->items ) :

				echo 'Items: ' . $order->currency->order->symbol . number_format( $order->discount->items, $order->currency->order->precision ) . "\n";

			endif;

		endif;

	endif;

	echo "\n";
	echo 'GRAND TOTAL' . "\n";
	echo $order->currency->order->symbol . number_format( $order->totals->grand, $order->currency->order->precision ) . "\n";



	if ( $order->voucher ) :

		echo 'The following voucher was used with this order:' . "\n";
		echo $order->voucher->code . ' - ' . $order->voucher->label . "\n";

	endif;


	if ( $order->requires_shipping ) :

		if ( $type == 'receipt' ) :

			echo 'The items in your order which require shipping will be shipped to the following address:' . "\n";

		elseif ( $type == 'notification' ) :

			echo 'The items in the order which require shipping must be shipped to the following address:' . "\n";

		endif;

		echo strtoupper( $order->shipping_details->addressee ) . "\n";
		echo $order->shipping_details->line_1 ? $order->shipping_details->line_1 . "\n" : '';
		echo $order->shipping_details->line_2 ? $order->shipping_details->line_2 . "\n" : '';
		echo $order->shipping_details->town ? $order->shipping_details->town . "\n" : '';
		echo $order->shipping_details->postcode ? $order->shipping_details->postcode . "\n" : '';
		echo $order->shipping_details->country ? $order->shipping_details->country . "\n" : '';
		echo $order->shipping_details->state ? $order->shipping_details->state . "\n" : '';

		if ( $type == 'receipt' ) :

			$_track_token = urlencode( $this->encrypt->encode( $order->ref . '|' . $order->id . '|' . time(), APP_PRIVATE_KEY ) );

			echo 'They will be shipped using ' . $order->shipping_method->courier . ' - ' . $order->shipping_method->method . '; you can also track the status of your order at the following URL:' . "\n";
			echo '{unwrap}' . site_url( shop_setting( 'shop_url' ) . 'order/track?token=' . $_track_token ) . '{unwrap}';

		elseif ( $type == 'notification' ) :

			echo 'They must be shipped using ' . $order->shipping_method->courier . ' - ' . $order->shipping_method->method . '.' . "\n";

		endif;

	
	endif;

