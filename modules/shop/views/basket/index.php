<div class="container shop basket">

	<?php
	
		if ( $basket->items ) :
		
			$this->load->view( 'shop/basket/basket-table' );
			
			if ( $payment_gateways ) :
			
				echo '<p class="checkout">';
				echo anchor( 'shop/checkout', 'Checkout', 'class="awesome"' );
				
				echo '<small>';
				
					echo 'We accept ';
					
					$_num = count( $payment_gateways );
					
					if ( $_num > 1 ) :
					
						for ( $i=0; $i < $_num; $i++ ) :
						
							if ( $i == $_num-1 ) :
							
								echo ' and ';
							
							elseif( $i != 0 ) :
							
								echo ', ';
							
							endif;
							
							// --------------------------------------------------------------------------
							
							if ( $payment_gateways[$i]->website ) :
							
								echo anchor( $payment_gateways[$i]->website, $payment_gateways[$i]->label );
							
							else :
							
								echo $payment_gateways[$i]->label;
								
							endif;
						
						endfor;
					
					else :
					
						if ( $payment_gateways[0]->website ) :
						
							echo anchor( $payment_gateways[0]->website, $payment_gateways[0]->label );
						
						else :
						
							echo $payment_gateways[0]->label;
							
						endif;
					
					endif;
				
				echo '</small>';
				
				echo '</p>';
			
			endif;
		
		else :
		
			?>
			<div class="basket-empty">
				<p>Your basket is currently empty</p>
			</div>
			<?php
		
		endif;
	
	?>

</div>