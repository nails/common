<p>
	Thanks for your order at <?=APP_NAME?>, here's your email receipt for your records.
</p>
<p>
	Order reference <strong><?=$order->ref?></strong>, placed on the <?=date( 'jS F Y, \a\t H:i:s', strtotime( $order->created ) )?>.
</p>
<table class="default-style">
	<thead>
		<tr>
			<th>Item</th>
			<th class="center">Quantity</th>
			<th class="center">Unit Price</th>
			<th class="center">Tax</th>
			<th class="center">Shipping</th>
		</tr>
	</thead>
	<tbody>
	<?php
	
		foreach ( $order->items AS $item ) :
		
			echo '<tr class="line-bottom">';
			echo '<td>';
			echo $item->title;
			echo '<small>' . $item->type->label . '; Product ID: ' . $item->product_id . '</small>';
			echo '</td>';
			echo '<td class="center">' . $item->quantity . '</td>';
			
			if ( $item->was_on_sale ) :
			
				echo '<td class="center">' . $order->currency->order->symbol . number_format( $item->sale_price, $order->currency->order->precision ) . '</td>';
				
			else :
			
				echo '<td class="center">' . $order->currency->order->symbol . number_format( $item->price, $order->currency->order->precision ) . '</td>';
			
			endif;
			
			echo '<td class="center">' . $order->currency->order->symbol . number_format( $item->tax, $order->currency->order->precision ) . '</td>';
			
			if ( $item->shipping ) :
			 
				echo '<td class="center">' . $order->currency->order->symbol . number_format( $item->shipping, $order->currency->order->precision ) . '</td>';
				
			else :
			
				echo '<td class="center">FREE</td>';
			
			endif;

			echo '</tr>';
		
		endforeach;
	
	?>
	
	<tr>
		<td colspan="4" class="right"><strong>Sub Total</strong></td>
		<td class="center"><?=$order->currency->order->symbol . number_format( $order->totals->sub, $order->currency->order->precision )?></td>
	</tr>
	<tr>
		<td colspan="4" class="right"><strong>Tax</strong></td>
		<td class="center"><?=$order->currency->order->symbol . number_format( $order->totals->tax, $order->currency->order->precision )?></td>
	</tr>
	<tr>
		<td colspan="4" class="right"><strong>Grand</strong></td>
		<td class="center"><?=$order->currency->order->symbol . number_format( $order->totals->grand, $order->currency->order->precision )?></td>
	</tr>
	</tbody>
</table>
</hr />