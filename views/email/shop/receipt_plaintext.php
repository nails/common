Thanks for your order at <?=APP_NAME?>, here's your email receipt for your records.

Order reference <strong><?=$order->ref?></strong>, placed on the <?=date( 'jS F Y, \a\t H:i:s', strtotime( $order->created ) )?>.

---------------

<?php

	foreach ( $order->items AS $item ) :
	
		echo $item->title . "\n";
		echo ' - ' . $item->type->label . '; Product ID: ' . $item->product_id . "\n";
		echo 'Quantity: ' . $item->quantity . "\n";
		
		if ( $item->was_on_sale ) :
		
			echo 'Price: ' . $order->currency->order->symbol . number_format( $item->sale_price, $order->currency->order->precision ) . "\n";
			
		else :
		
			echo 'Price: ' . $order->currency->order->symbol . number_format( $item->price, $order->currency->order->precision ) . "\n";
		
		endif;
		
		echo 'Tax: ' . $order->currency->order->symbol . number_format( $item->tax, $order->currency->order->precision ) . "\n";
		
		if ( $item->shipping ) :
		 
			echo 'Shipping: ' . $order->currency->order->symbol . number_format( $item->shipping, $order->currency->order->precision ) . "\n";
			
		else :
		
			echo 'Shipping: FREE' . "\n";
		
		endif;
		
		echo "\n";
	
	endforeach;

?>


Sub Total: <?=$order->currency->order->symbol . number_format( $order->totals->sub, $order->currency->order->precision )?>

Tax: <?=$order->currency->order->symbol . number_format( $order->totals->tax, $order->currency->order->precision )?>

Grand Total: <?=$order->currency->order->symbol . number_format( $order->totals->grand, $order->currency->order->precision )?>