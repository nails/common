<div class="container shop checkout basket confirm">

	<!--	BASKET	-->
	<?php


		$this->load->view( 'shop/basket/basket-table', array( 'no_changes' => TRUE ) );


		//	Vouchers
		if ( $basket->voucher ) :

			echo '<div class="vouchers sixteen columns first last row">';

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

				echo '</span>';
				echo '</div>';

			echo '</div>';

			echo '</div>';

		endif;


	?>
	
	<!--	SHIPPING	-->
	<?php

		if ( $basket->requires_shipping ) :

			echo '<section class="row sixteen columns first last">';
			echo '<h2>Shipping Details</h2>';
			echo '<p>';

				echo '<strong>' . $basket->shipping_details->addressee . '</strong>';
				echo $basket->shipping_details->line_1 ? '<br />' . $basket->shipping_details->line_1 : '';
				echo $basket->shipping_details->line_2 ? '<br />' . $basket->shipping_details->line_2 : '';
				echo $basket->shipping_details->town ? '<br />' . $basket->shipping_details->town : '';
				echo $basket->shipping_details->postcode ? '<br />' . $basket->shipping_details->postcode : '';
				echo $basket->shipping_details->country ? '<br />' . $basket->shipping_details->country : '';
				echo $basket->shipping_details->state ? '<br />' . $basket->shipping_details->state : '';

			echo '</p>';
			echo '</section>';

		endif;

		if ( $basket->payment_gateway ) :

			echo '<section class="row sixteen columns first last">';
			echo '<h2 class="row">Payment Option</h2>';
			
			echo '<ul class="payment-gateways">';
			foreach ($payment_gateways AS $pg ) :


				if ( $pg->id == $basket->payment_gateway ) :

					echo '<li>';
					echo '<label style="cursor:default;">';
					if ( $pg->logo ) :
					
						echo img( NAILS_URL . 'img/modules/shop/payment-gateway/' . $pg->logo );
						
					else :
					
						echo $pg->label;
					
					endif;
					echo '</label>';
					echo '</li>';

				endif;

			endforeach;
			echo '</ul>';
			echo '</section>';

		endif;

		$_uri_back = shop_setting( 'shop_url' ) . 'checkout';
		$_uri_back .= $guest ? '?guest=true' : '';

		$_uri_pay = shop_setting( 'shop_url' ) . 'checkout/payment';
		$_uri_pay .= $guest ? '?guest=true' : '';

		echo '<div class="row sixteen columns first last">';
		echo '<hr />';
		echo anchor( $_uri_back, lang( 'action_back' ), 'class="awesome small"' );
		echo anchor( $_uri_pay, lang( 'action_continue' ), 'class="awesome" style="float:right;margin-right:0;"' );
		echo '</div>';

	?>
	
</div>