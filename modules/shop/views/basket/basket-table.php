<table>
	<thead>
		<tr>
			<th class="item">Item</th>
			<th class="quantity">Quantity</th>
			<th class="price">Unit Price</th>
			<th class="tax">Tax</th>
			<th class="shipping">Shipping</th>
			<th class="total">Total</th>
		</tr>
	</thead>
	<tbody>
	
		<!--	ITEMS	-->
		<?php
		
			$_i = 0;
			
			foreach ( $basket->items AS $key => $item ) :
			
				$_stripe = $_i % 2 ? 'odd' : 'even';
				$_i++;
				
				?>
				<tr data-product_id="<?=$item->id?>" data-key="<?=$key?>" class="<?=$_stripe?>">
					<td class="item">
						<div class="img <?=$item->type->slug?>">
							<!--	PRODUCT'S PRIMARY IMAGE	-->
						</div>
						<?=$item->title?>
						<small>
							<?=$item->type->label?>,
							Product ID: <?=$item->id?>
						</small>
					</td>
					<td class="quantity">
					<?php
					
						//	Decrement
						if ( ! isset( $no_changes ) || ! $no_changes ) :
						
							echo anchor( 'shop/basket/decrement/' . $item->id, 'Decrement', 'class="decrement"' );
							
						endif;
						
						//	Quantity
						echo '<span class="value">' . $item->quantity . '</span>';
						
						//	Increment
						if ( ! isset( $no_changes ) || ! $no_changes ) :
						
							if ( is_null( $item->type->max_per_order ) || $item->quantity < $item->type->max_per_order ) :
							
								echo anchor( 'shop/basket/increment/' . $item->id, 'Increment', 'class="increment"' );
								
							endif;
						
						endif;
						
					?>
					</td>
					<?php
					
						if ( $item->is_on_sale ) :
						
							echo '<td class="price on-sale">';
							echo '<span>' . SHOP_USER_CURRENCY_SYMBOL . $item->sale_price . '</span>';
							echo '<span class="ribbon"></span>';
							echo '<del>was ' . SHOP_USER_CURRENCY_SYMBOL . $item->price . '</del>';
							echo '</td>';
						
						else :
						
							echo '<td class="price">';
							echo SHOP_USER_CURRENCY_SYMBOL . $item->price;
							echo '</td>';
						
						endif;
						
					?>
					<td class="tax"><?=$item->tax_rate?></td>
					<?php
					
						if ( $item->shipping ) :
						
							echo '<td class="shipping">';
							echo SHOP_USER_CURRENCY_SYMBOL . $item->shipping;
							echo '</td>';
						
						else :
						
							echo '<td class="shipping free">';
							echo 'FREE';
							echo '</td>';
						
						endif;
						
					?>
					<td class="total"><?=SHOP_USER_CURRENCY_SYMBOL . $item->total?></td>
				</tr>
				<?php
		
			endforeach;
			
		?>
		
		<!--	TOTALS	-->
		<tr class="total sub">
			<td class="label" colspan="4">Sub Total</td>
			<td class="value">
			<?php
				
				if ( $basket->totals->shipping ) :
				
					echo SHOP_USER_CURRENCY_SYMBOL . $basket->totals->shipping;
				
				else :
				
					echo 'FREE';
					
				endif;
				
			?>
			</td>
			<td class="value"><?=$basket->totals->sub?></td>
		</tr>
		<tr class="total grand">
			<td class="label" colspan="4">TAX</td>
			<td class="value">&nbsp;</td>
			<td class="value"><?=SHOP_USER_CURRENCY_SYMBOL . $basket->totals->tax?></td>
		</tr>
		<tr class="total grand">
			<td class="label" colspan="4">Grand Total</td>
			<td class="value">&nbsp;</td>
			<td class="value"><?=SHOP_USER_CURRENCY_SYMBOL . $basket->totals->grand?></td>
		</tr>
		
	</tbody>
</table>