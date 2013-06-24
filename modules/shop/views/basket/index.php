<div class="container shop basket">

	<?php
	
		if ( $basket->items ) :

			//	Voucher removed?
			if ( isset( $basket->voucher_removed ) && $basket->voucher_removed ) :

				echo '<div class="system-alert message sixteen columns first last row">';
				echo '<div class="padder">';
				echo '<p><strong>We removed your voucher:</strong> ' . $basket->voucher_removed . '</p>';
				echo '</div>';
				echo '</div>';

			endif;
		
			$this->load->view( 'shop/basket/basket-table', array( 'show_shipping_chooser' => TRUE ) );

			// --------------------------------------------------------------------------

			//	Vouchers
			echo '<div class="vouchers sixteen columns first last row">';

				if ( $basket->voucher ) :

					echo '<div class="voucher row ' . strtolower( $basket->voucher->type ) . '">';

					echo '<div class="four columns first">';
					echo '<span class="code">';

					switch( $basket->voucher->type ) :

						case 'GIFT_CARD' :	echo img( NAILS_URL . 'img/modules/shop/basket/ribbon-gift-card.png' );	break;
						default:			echo img( NAILS_URL . 'img/modules/shop/basket/ribbon-voucher.png' );	break;

					endswitch;

					echo $basket->voucher->code;
					echo '</span>';
					echo '</div>';

					echo '<div class="twelve columns last">';
					echo '<span class="label">';

					switch( $basket->voucher->type ) :

						case 'GIFT_CARD' :

							echo '<strong>' . APP_NAME . ' Gift Card - ' . shop_format_price( $basket->voucher->discount_value, TRUE ) . '</strong>';
							echo '<small>Remaining balance: ' . shop_format_price( $basket->voucher->gift_card_balance, TRUE ) . '</small>';

						break;

						// --------------------------------------------------------------------------

						default:

							echo $basket->voucher->label;;

						break;

					endswitch;

					echo anchor( shop_setting( 'shop_url' ) . 'basket/remove_voucher', 'Remove' );
					echo '</span>';
					echo '</div>';

					echo '</div>';

				else :

					//	Add voucher
					echo '<div class="voucher-add row">';

					echo form_open( shop_setting( 'shop_url' ) . 'basket/add_voucher' );
					echo form_input( 'voucher', NULL, 'class="working" placeholder="Got a discount voucher or giftcard code? Enter it here..."' );
					echo form_submit( 'submit', 'Validate', 'class="awesome"' );
					echo form_close();
					echo '<script type="text/javascript">';
					echo '$( \'.voucher-add form\').on( \'submit\', function () { $(this).addClass( \'working\' ); } )';
					echo '</script>';
					echo '</div>';

				endif;

			echo '</div>';

			// --------------------------------------------------------------------------

			//	Free shipping?
			if ( shop_setting( 'free_shipping_threshold' ) && $basket->requires_shipping ) :

				if ( $basket->totals->sub  < shop_setting( 'free_shipping_threshold' ) ) :

					$_amount_left = shop_setting( 'free_shipping_threshold' ) - $basket->totals->sub;
					$_amount_left = shop_format_price( $_amount_left, TRUE );

					echo '<div class="free-shipping-threshold row">';
					echo '<p>Spend another <span class="amount-left">' . $_amount_left . '</span> to receive free shipping!</p>';
					echo '</div>';

				else :

					echo '<div class="free-shipping-threshold row">';
					echo '<p>';
					echo 'Your order qualifies for free shipping!';
					echo '<small>Your order qualifies because you\'ve spent more than ' . shop_format_price( shop_setting( 'free_shipping_threshold' ), TRUE ) . '</small>';
					echo '</p>';
					echo '</div>';

				endif;

			endif;

			// --------------------------------------------------------------------------
			
			if ( $payment_gateways ) :
			
				echo '<p class="checkout">';
				echo anchor( shop_setting( 'shop_url' ) . 'checkout', 'Checkout', 'class="awesome huge"' );
				
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