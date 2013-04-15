<div class="container shop basket">

	<?php
	
		if ( $basket->items ) :
		
			?>
			<table>
				<thead>
					<tr>
						<th class="item">Item</th>
						<th class="quantity">Quantity</th>
						<th class="price">Unit Price</th>
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
							<tr data-key="<?=$key?>" class="<?=$_stripe?>">
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
									<?=anchor( 'shop/basket/increment/' . $key, 'Increment', 'class="increment"' )?>
									<span class="value"><?=$item->quantity?></span>
									<?=anchor( 'shop/basket/decrement/' . $key, 'Decrement', 'class="decrement"' )?>
								</td>
								<td class="price"><?=$item->price?></td>
								<td class="shipping"><?=$item->shipping?></td>
								<td class="total"><?=$item->total?></td>
							</tr>
							<?php
					
						endforeach;
						
					?>
					
					<!--	TOTALS	-->
					<tr class="total sub">
						<td class="label" colspan="3">Sub Total</td>
						<td class="value">Free</td>
						<td class="value">&pound;34.99</td>
					</tr>
					<tr class="total tax">
						<td class="label" colspan="3">VAT</td>
						<td class="value">&nbsp;</td>
						<td class="value">&pound;0.00</td>
					</tr>
					<tr class="total grand">
						<td class="label" colspan="3">Grand Total</td>
						<td class="value">&nbsp;</td>
						<td class="value">&pound;34.99</td>
					</tr>
					
				</tbody>
			</table>
			
			<p class="checkout">
				<?=anchor( 'shop/checkout', 'Checkout', 'class="awesome"' )?>
				<div class="clear"></div>
			</p>
			<?php
		
		else :
		
			?>
			<div class="basket-empty">
				<p>Your basket is currently empty</p>
			</div>
			<?
		
		endif;
	
	?>

</div>